<?php

namespace Modules\Api\V1\Merchant\General\Http\Controllers;

use App\Enums\AccountTypes;
use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Account\Account;
use App\Models\Merchant\MerchantOrder;
use App\Models\Merchant\MerchantTransaction;
use App\Models\Order\OrderPayment;
use App\Models\Shop\ShopSetting;
use App\Models\Stock\StockOrder;
use App\Services\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class WebhookController extends Controller
{
    public function webhook(Request $request): JsonResponse
    {
        $sfcConfig = ShopSetting::whereIn('key', ['sfc_webhook_token'])
            ->pluck('value', 'key')
            ->toArray();

        $sfcWebhookKey = $sfcConfig['sfc_webhook_token'] ?? null;
        $token = $request->bearerToken();

        if (!$sfcWebhookKey || !$token || $token !== $sfcWebhookKey) {
            Log::warning('Unauthorized webhook access attempt');
            return ApiResponse::failure('Unauthorized', Response::HTTP_UNAUTHORIZED);
        }

        $validatedData = $request->validate([
            'consignment_id' => 'required|exists:merchant_orders,consignment_id',
            'status' => 'required|string',
            'delivery_charge' => 'nullable|numeric',
            'cod_amount' => 'nullable|numeric',
        ]);

        try {
            DB::beginTransaction();

            $statusKey = strtolower($validatedData['status']);

            $statusMapping = [
                'pending' => OrderStatus::PENDING->value,
                'delivered' => OrderStatus::DELIVERED->value,
                'partial_delivered' => OrderStatus::PARTIAL_DELIVERED->value,
                'cancelled' => OrderStatus::CANCELLED->value,
                'unknown' => OrderStatus::UNKNOWN->value,
            ];

            $statusId = $statusMapping[$statusKey] ?? null;

            if (!$statusId) {
                return ApiResponse::failure('Invalid status', Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $merchantOrder = MerchantOrder::where('consignment_id', $validatedData['consignment_id'])->first();

            if (!$merchantOrder) {
                return ApiResponse::failure('Merchant order not found', Response::HTTP_NOT_FOUND);
            }

            // 💰 Safe values
            $deliveryCharge = (float) ($validatedData['delivery_charge'] ?? 0);
            $newCod = (float) ($validatedData['cod_amount'] ?? 0);
            $originalCod = (float) $merchantOrder->codAmount();

            // 📊 Calculate mismatch
            $fineAmt = max(0, $deliveryCharge - $merchantOrder->shipping_amount);
            $savedAmt = max(0, $merchantOrder->shipping_amount - $deliveryCharge);

            $hasMismatch = ($fineAmt > 0) || ($savedAmt > 0) || ($newCod > 0 && $newCod !== $originalCod);

            // Save mismatch-related data
            $merchantOrder->fine_amount = $fineAmt;
            $merchantOrder->delivery_amount_saved = $savedAmt;
            $merchantOrder->new_cod = $newCod;
            $merchantOrder->courier_status = $statusKey;

            if ($hasMismatch) {
                $merchantOrder->mismatch_detected_at ??= now();

                // 🚫 BLOCK status update
                Log::warning('Mismatch detected. Status update blocked.', [
                    'order_id' => $merchantOrder->id,
                    'consignment_id' => $merchantOrder->consignment_id
                ]);
            } else {

                // ✅ CLEAR mismatch
                $merchantOrder->mismatch_detected_at = null;

                // ✅ Update timeline
                $merchantOrder->orderTimeLines()->updateOrCreate(
                    ['status_id' => $statusId],
                    ['date' => now()]
                );

                // ✅ Update status
                if ($statusId !== OrderStatus::PENDING->value) {
                    $merchantOrder->status_id = $statusId;
                }

                // 🚚 Delivered logic (safe)
                if ($statusId === OrderStatus::DELIVERED->value && !$merchantOrder->delivered_at) {

                    $merchantOrder->delivered_at = now();

                    $merchant = $merchantOrder->merchant;

                    $payableAmount = $merchantOrder->grand_total
                        - $deliveryCharge
                        - $fineAmt;

                    $merchant->increment('withdrawal_balance', $payableAmount);

                    $merchantOrder->items()
                        ->where('status_id', OrderStatus::READY_TO_SHIP->value)
                        ->update(['status_id' => OrderStatus::DELIVERED->value]);

                    if ($merchantOrder->payment) {
                        $merchantOrder->payment->update([
                            'payment_status' => OrderPayment::$PAID
                        ]);
                    }

                    $this->ledgerCreate($merchantOrder);
                }
            }

            $merchantOrder->save();

            DB::commit();

            return ApiResponse::success(
                $hasMismatch
                ? 'Mismatch detected. Status not updated.'
                : 'Status updated successfully.'
            );

        } catch (\Throwable $th) {
            DB::rollBack();

            Log::error('Webhook Exception', [
                'message' => $th->getMessage(),
            ]);

            return ApiResponse::failure('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function ledgerCreate(MerchantOrder $merchantOrder)
    {
        $calculateTotal = $this->calculateTotal($merchantOrder);
        $uuid = Str::uuid();
        $fineAmount = $merchantOrder->fine_amount;
        $totalAmount = $merchantOrder->total_amount;
        $totalPurchasePrice = $calculateTotal->totalPurchasePrice;
        $totalItemsDiscount = $merchantOrder->item_discount;
        $totalDiscountAmount = $merchantOrder->discount_amount;
        $totalShippingCost = $merchantOrder->shipping_amount;
        $paidShippingCost = $totalShippingCost - $merchantOrder->delivery_amount_saved;
        $totalCommission = $calculateTotal->totalCommission;

        $amountDifference = $totalAmount - $totalPurchasePrice;
        $grossProfit = $amountDifference - $totalItemsDiscount - $totalDiscountAmount;

        $netProfit = $grossProfit - $totalCommission;

        $this->updateAccountBalance($merchantOrder->merchant_id, $totalAmount, AccountTypes::ASSET->value, 'PYRC', 'debit', 'increment', $uuid);
        $this->updateAccountBalance($merchantOrder->merchant_id, $totalAmount, AccountTypes::INCOME->value, 'REVE', 'credit', 'increment', $uuid);
        $this->updateAccountBalance($merchantOrder->merchant_id, $totalPurchasePrice, AccountTypes::INVENTORY->value, 'INAS', 'credit', 'decrement', $uuid);
        $this->updateAccountBalance($merchantOrder->merchant_id, $totalPurchasePrice, AccountTypes::SALE->value, 'COGS', 'debit', 'increment', $uuid);

        if ($totalItemsDiscount > 0) {
            $this->updateAccountBalance($merchantOrder->merchant_id, $totalItemsDiscount, AccountTypes::ASSET->value, 'PYRC', 'credit', 'decrement', $uuid);
            $this->updateAccountBalance($merchantOrder->merchant_id, $totalItemsDiscount, AccountTypes::EXPENSE->value, 'PROD', 'debit', 'increment', $uuid);
        }

        if ($totalDiscountAmount > 0) {
            $this->updateAccountBalance($merchantOrder->merchant_id, $totalDiscountAmount, AccountTypes::ASSET->value, 'PYRC', 'credit', 'decrement', $uuid);
            $this->updateAccountBalance($merchantOrder->merchant_id, $totalDiscountAmount, AccountTypes::EXPENSE->value, 'PROD', 'debit', 'increment', $uuid);
        }

        if ($totalShippingCost > 0) {
            $this->updateAccountBalance($merchantOrder->merchant_id, $totalShippingCost, AccountTypes::ASSET->value, 'PYRC', 'debit', 'increment', $uuid);
            $this->updateAccountBalance($merchantOrder->merchant_id, $totalShippingCost, AccountTypes::INCOME->value, 'REVE', 'credit', 'increment', $uuid);
            $this->updateAccountBalance($merchantOrder->merchant_id, $paidShippingCost, AccountTypes::INCOME->value, 'REVE', 'debit', 'decrement', $uuid);
            $this->updateAccountBalance($merchantOrder->merchant_id, $paidShippingCost, AccountTypes::ASSET->value, 'PYRC', 'credit', 'decrement', $uuid);
            $this->updateAccountBalance($merchantOrder->merchant_id, $paidShippingCost, AccountTypes::SALE->value, 'SHPC', 'credit', 'increment', $uuid);
        }

        if ($fineAmount > 0) {
            $this->updateAccountBalance($merchantOrder->merchant_id, $fineAmount, AccountTypes::ASSET->value, 'PYRC', 'credit', 'decrement', $uuid);
            $this->updateAccountBalance($merchantOrder->merchant_id, $fineAmount, AccountTypes::EXPENSE->value, 'SHFI', 'debit', 'increment', $uuid);
        }

        if ($totalCommission > 0) {
            $this->updateAccountBalance($merchantOrder->merchant_id, $totalCommission, AccountTypes::ASSET->value, 'PYRC', 'credit', 'decrement', $uuid);
            $this->updateAccountBalance($merchantOrder->merchant_id, $totalCommission, AccountTypes::EXPENSE->value, 'COMM', 'debit', 'increment', $uuid);
        }

        $this->updateAccountBalance($merchantOrder->merchant_id, $merchantOrder->grand_total, AccountTypes::SALE->value, 'SALE', 'debit', 'increment', $uuid);

        $this->updateAccountBalance($merchantOrder->merchant_id, $netProfit, AccountTypes::SALE->value, 'NETP', 'credit', 'increment', $uuid);
        $this->updateAccountBalance($merchantOrder->merchant_id, $grossProfit, AccountTypes::SALE->value, 'GRPF', 'credit', 'increment', $uuid);
    }

    public function calculateTotal($merchantOrder)
    {
        $totalPurchasePrice = 0;
        $totalCommission = 0;
        $saleProductDetails = $merchantOrder->items()
            ->where('status_id', OrderStatus::DELIVERED->value)
            ->get();

        foreach ($saleProductDetails as $detail) {
            $purchase_price = StockOrder::where(['type' => 2, 'sell_product_detail_id' => $detail->id])->sum('purchase_price');
            $totalCommission += $detail->commission;
            $totalPurchasePrice += $purchase_price;
        }

        return (object) [
            'totalCommission' => $totalCommission,
            'totalPurchasePrice' => $totalPurchasePrice,
        ];
    }

    public function updateAccountBalance($merchantId, $amount, $accountType, $uucode = null, $type = 'credit', $method = 'increment', $uuid = null)
    {
        $account = Account::where('merchant_id', $merchantId)
            ->where('account_type', $accountType)
            ->when($uucode, function ($query, $uucode) {
                $query->where('uucode', $uucode);
            })
            ->first();

        $account->{$method}('balance', $amount);

        MerchantTransaction::create([
            'uuid' => $uuid,
            'merchant_id' => $merchantId,
            'account_id' => $account->id,
            'amount' => $method === 'increment' ? $amount : -$amount,
            'date' => now(),
            'type' => $type,
        ]);
    }
}