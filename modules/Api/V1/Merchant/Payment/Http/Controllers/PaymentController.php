<?php

namespace Modules\Api\V1\Merchant\Payment\Http\Controllers;

use App\Enums\AccountTypes;
use App\Http\Controllers\Controller;
use Modules\Api\V1\Merchant\Payment\Http\Requests\PurchasePaymentRequest;
use Modules\Api\V1\Merchant\Payment\Http\Requests\SellPaymentRequest;
use App\Models\Account\Account;
use App\Models\Merchant\MerchantTransaction;
use App\Models\Purchase\Purchase;
use App\Models\Sell\SellProduct;
use App\Models\Supplier\Supplier;
use App\Models\Supplier\SupplierPurchasePayment;
use App\Services\ApiResponse;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('shop.permission:create-purchase-payment')->only('storePurchasePayment');
        $this->middleware('shop.permission:create-sell-payment')->only('storeSellPayment');
    }

    public function storePurchasePayment(PurchasePaymentRequest $request): JsonResponse
    {
        try {
            $merchantId = $request->user()->merchant->id;
            $validated  = $request->validated();

            $purchase = Purchase::where('merchant_id', $merchantId)->findOrFail($validated['purchase_id']);
            $account  = Account::where('merchant_id', $merchantId)->findOrFail($validated['wallet_id']);
            $supplier = Supplier::findOrFail($validated['supplier_id']);

            DB::beginTransaction();

            $grandTotal = $purchase->grand_total;
            $paidAmount = $validated['amount'];

            if ($account->balance < $paidAmount) {
                return ApiResponse::failure('Insufficient wallet balance', Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            if ($paidAmount > $grandTotal || $purchase->paid_amount + $paidAmount > $grandTotal) {
                return ApiResponse::failure('Payment amount cannot exceed order amount of ' . $grandTotal - $purchase->paid_amount, Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $payableAmount   = $purchase->due_amount - $paidAmount;
            $supplierPayment = $purchase->supplierPurchasePayment()->updateOrCreate([
                'supplier_id' => $supplier->id,
                'merchant_id' => $merchantId,
            ], [
                'paid_status'  => $payableAmount == 0 ? SupplierPurchasePayment::$FULL_DUE : SupplierPurchasePayment::$PARTIAL_PAID,
                'paid_amount'  => $paidAmount,
                'due_amount'   => $purchase->due_amount - $paidAmount,
                'total_amount' => $grandTotal,
            ]);
            if ($request->hasFile('attachment')) {
                $supplierPayment->attachment = $request->file('attachment');
            }
            $purchase->update([
                'payment_status_id' => $payableAmount == 0 ? Purchase::$PAYMENT_STATUS_PAID : Purchase::$PAYMENT_STATUS_PARTIAL,
                'due_amount'        => $purchase->due_amount - $paidAmount,
                'paid_amount'       => $purchase->paid_amount + $paidAmount,
            ]);

            $uuid        = Str::uuid();
            $paymentDate = $validated['date'] ?? now();

            $supplierPayment->supplierPurchasePaymentDetails()->create([
                'account_id'          => $account->id,
                'current_paid_amount' => $paidAmount,
                'date'                => $paymentDate,
                'note'                => $validated['note']   ?? null,
                'reference'           => $validated['ref_no'] ?? null,
            ]);

            $account->decrement('balance', $paidAmount);

            MerchantTransaction::create([
                'uuid'        => $uuid,
                'merchant_id' => $merchantId,
                'account_id'  => $account->id,
                'amount'      => -$paidAmount,
                'date'        => $paymentDate,
                'type'        => 'credit',
                'reason'      => 'Supplier Payment',
            ]);

            $accountsPayable = Account::where([
                'merchant_id'  => $merchantId,
                'account_type' => AccountTypes::LIABILITIES->value,
                'uucode'       => 'ACPA',
            ])->firstOrFail();

            $accountsPayable->decrement('balance', $paidAmount);

            MerchantTransaction::create([
                'uuid'        => $uuid,
                'merchant_id' => $merchantId,
                'account_id'  => $accountsPayable->id,
                'amount'      => -$grandTotal,
                'date'        => $paymentDate,
                'type'        => 'debit',
                'reason'      => 'Supplier Payment',
            ]);

            $supplier->decrement('balance', $paidAmount);

            DB::commit();

            return ApiResponse::success('Payment successful.', [], Response::HTTP_CREATED);
        } catch (ModelNotFoundException) {
            return ApiResponse::failure('Purchase info, wallet, or supplier not found.', Response::HTTP_NOT_FOUND);
        } catch (ValidationException $e) {
            return ApiResponse::validationError('Validation errors occurred.', $e->validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Exception $e) {
            DB::rollBack();

            return ApiResponse::failure('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function storeSellPayment(SellPaymentRequest $request): JsonResponse
    {
        try {
            $merchantId = $request->user()->merchant->id;
            $validated  = $request->validated();

            $sellProduct = SellProduct::where('merchant_id', $merchantId)->findOrFail($validated['sell_product_id']);
            $account     = Account::where('merchant_id', $merchantId)->findOrFail($validated['account_id']);

            DB::beginTransaction();
            $grandTotal = $sellProduct->grand_total;
            $paidAmount = $validated['amount'];

            if ($paidAmount > $grandTotal || $sellProduct->paid_amount + $paidAmount > $grandTotal) {
                return ApiResponse::failure('Payment amount cannot exceed order amount of ' . $grandTotal - $sellProduct->paid_amount, Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $newDueAmount  = $sellProduct->due_amount - $paidAmount;
            $newPaidAmount = $sellProduct->paid_amount + $paidAmount;

            $sellPaymentStatus = match ($paidAmount) {
                0           => SupplierPurchasePayment::$FULL_DUE,
                $grandTotal => SupplierPurchasePayment::$FULL_PAID,
                default     => SupplierPurchasePayment::$PARTIAL_PAID,
            };

            $sellPayment = $sellProduct->sellPayments()->updateOrCreate(
                [
                    'merchant_id' => $merchantId,
                    'customer_id' => $sellProduct->customer_id,
                ],
                [
                    'paid_status'  => $sellPaymentStatus,
                    'paid_amount'  => $paidAmount,
                    'due_amount'   => $newDueAmount,
                    'total_amount' => $grandTotal,
                ]
            );

            $sellProduct->update([
                'payment_status' => $newDueAmount == 0 ? 2 : 1,
                'due_amount'     => $newDueAmount,
                'paid_amount'    => $newPaidAmount,
            ]);

            $uuid        = Str::uuid();
            $paymentDate = $validated['date'] ?? now();

            $sellPayment->sellPaymentDetails()->create([
                'account_id'          => $account->id,
                'current_paid_amount' => $paidAmount,
                'date'                => $paymentDate,
                'note'                => $validated['note']   ?? null,
                'ref_no'              => $validated['ref_no'] ?? null,
            ]);

            $account->increment('balance', $paidAmount);

            MerchantTransaction::create([
                'uuid'        => $uuid,
                'merchant_id' => $merchantId,
                'account_id'  => $account->id,
                'amount'      => $paidAmount,
                'date'        => $paymentDate,
                'type'        => 'debit',
                'reason'      => 'Sell Product Payment',
            ]);

            $salePayableReceivable = Account::where([
                'merchant_id'  => $merchantId,
                'account_type' => AccountTypes::ASSET->value,
                'uucode'       => 'SPRC',
            ])->firstOrFail();

            $salePayableReceivable->decrement('balance', $paidAmount);

            MerchantTransaction::create([
                'uuid'        => $uuid,
                'merchant_id' => $merchantId,
                'account_id'  => $salePayableReceivable->id,
                'amount'      => -$paidAmount,
                'date'        => $paymentDate,
                'type'        => 'debit',
                'reason'      => 'Sell Product Payment Receivable',
            ]);

            DB::commit();

            return ApiResponse::success('Payment successful.', [], Response::HTTP_CREATED);
        } catch (ModelNotFoundException) {
            return ApiResponse::failure('Sell Product info or wallet not found.', Response::HTTP_NOT_FOUND);
        } catch (ValidationException $e) {
            return ApiResponse::validationError('Validation errors occurred.', $e->validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Exception $e) {
            DB::rollBack();

            return ApiResponse::failure('Something went wrong', Response::HTTP_BAD_REQUEST);
        }
    }
}
