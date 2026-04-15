<?php

namespace Modules\Api\V1\Merchant\Sale\Http\Controllers;

use Exception;
use Throwable;
use App\Enums\AccountTypes;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\ApiResponse;
use App\Models\Account\Account;
use App\Models\Sell\SellProduct;
use App\Models\Customer\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\SellProductService;
use App\Exceptions\InsufficientException;
use Illuminate\Support\Facades\Validator;
use App\Models\Merchant\MerchantTransaction;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Api\V1\Merchant\Sale\Http\Requests\SellProductRequest;
use Modules\Api\V1\Merchant\Sale\Http\Requests\ShippingFeeRequest;
use Modules\Api\V1\Merchant\Sale\Http\Requests\SellProductStatusChangeRequest;

class SellProductController extends Controller
{
    protected SellProductService $sellProductService;

    public function __construct(SellProductService $service)
    {
        $this->sellProductService = $service;
        $this->middleware('shop.permission:show-sales')->only('index', 'show');
        $this->middleware('shop.permission:create-sale')->only('store');
        $this->middleware('shop.permission:update-sale')->only('sellUpdate', 'sellStatusChange', 'manageShippingFee');
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $salesQuery = SellProduct::with(['sell_product_details', 'customer', 'sellPayment', 'sellPayment.sellPaymentDetails', 'sellPayment.sellPaymentDetails.account'])
                ->where('merchant_id', auth()->user()->merchant->id)
                ->whereNot('sold_from', 'Ecommerce')
                ->when($request->customer_id, function ($query) use ($request) {
                    // Filter by customer if provided
                    return $query->where('customer_id', $request->customer_id);
                })
                ->when($request->sale_date, function ($query) use ($request) {
                    return $query->whereDate('sale_date', $request->sale_date);
                })
                ->when($request->has('invoice_no'), function ($query) use ($request) {
                    return $query->where('invoice_no', $request->invoice_no);
                })
                ->when($request->has('start_date') && $request->has('end_date'), function ($query) use ($request) {
                    return $query->whereBetween('sale_date', [$request->start_date, $request->end_date]);
                })
                ->when($request->has('customer'), function ($query) use ($request) {
                    return $query->where('customer_id', $request->customer);
                });

            $sales = $salesQuery->orderBy('id', 'desc')->paginate($request->query('per_page', 10));

            return ApiResponse::formatPagination('Sales list retrieved successfully', $sales, Response::HTTP_OK);
        } catch (Exception $e) {
            return ApiResponse::failure('Something went wrong', Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @throws Throwable
     */
    public function store(SellProductRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $request->validated();

            // request merge
            $request->merge([
                'merchant_id' => $request->user()->merchant->id,
            ]);

            $customer = Customer::where('id', $request->customer_id)->where('merchant_id', $request->merchant_id)->first();

            if (! $customer) {
                return ApiResponse::failure('Customer not found', Response::HTTP_NOT_FOUND);
            }

            $saleProduct = $this->sellProductService->createSaleProduct($request);

            if ($request->hasFile('attachment')) {
                $saleProduct->addMedia($request->file('attachment'), 'attachment');
            }

            $products = json_decode($request->products, true);
            $accountId = $request->input('account_id');

            $this->sellProductService->processProducts($products, $saleProduct, $request);
            $this->sellProductService->handleTransaction($saleProduct, $accountId);

            $sellPayment = $accountId ? $this->sellProductService->createSellPayment($saleProduct, [
                'account_id' => $accountId,
                'date'       => $request->date ?? now(),
                'note'       => $request->note,
                'ref_no'     => $request->ref_no,
            ]) : null;
            DB::commit();

            return ApiResponse::success('Sale created successfully', [
                'id' => $saleProduct->id,
            ], Response::HTTP_CREATED);
        } catch (InsufficientException $e) {
            DB::rollBack();
            return ApiResponse::failure($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (ValidationException $e) {
            DB::rollBack();
            return ApiResponse::validationError('Validation Error', $e->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Exception $e) {
            DB::rollBack();

            return ApiResponse::failure('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $sales = SellProduct::with(['sell_product_details.product:id,name', 'customer', 'sellPayment', 'sellPayment.sellPaymentDetails', 'sellPayment.sellPaymentDetails.account'])
                ->where('merchant_id', auth()->user()->merchant->id)
                ->findOrFail($id);

            return ApiResponse::success('Sales detail retrieved successfully', $sales, Response::HTTP_OK);
        } catch (ModelNotFoundException) {
            return ApiResponse::failure('Sell product not found.', Response::HTTP_NOT_FOUND);
        }
    }

    public function sellStatusChange(SellProductStatusChangeRequest $request, int $id): JsonResponse
    {
        try {
            $sellProduct = SellProduct::where('merchant_id', auth()->user()->merchant->id)->findOrFail($id);
            $request->validated();

            $sellProduct->update(['sell_status_id' => $request->sell_status_id]);

            return ApiResponse::success('Sell status updated successfully.', $sellProduct);
        } catch (ModelNotFoundException) {
            return ApiResponse::failure('Sell info not found', Response::HTTP_NOT_FOUND);
        } catch (ValidationException $v) {
            return ApiResponse::validationError('There were validation errors.', $v->validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Exception $e) {
            return ApiResponse::failure('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function manageShippingFee(ShippingFeeRequest $request): JsonResponse
    {
        try {
            $request->validated();

            $sellProduct = SellProduct::where('merchant_id', auth()->user()->merchant->id)->findOrFail($request->sell_product_id);
            $sellProduct->update([
                'extra_shipping_fee'    => max(0, $request->shipping_fee - $sellProduct->total_shipping_cost),
                'delivery_amount_saved' => max(0, $sellProduct->total_shipping_cost - $request->shipping_fee),
            ]);

            $uuid             = Str::uuid();
            $fineAmount       = $sellProduct->extra_shipping_fee;
            $paidShippingCost = $sellProduct->total_shipping_cost - $sellProduct->delivery_amount_saved;

            if ($paidShippingCost > 0) {
                $this->updateAccountBalance($sellProduct->merchant_id, $paidShippingCost, AccountTypes::INCOME->value, 'REVE', 'debit', 'decrement', $uuid);
                $this->updateAccountBalance($sellProduct->merchant_id, $paidShippingCost, AccountTypes::ASSET->value, 'SPRC', 'credit', 'decrement', $uuid);
                $this->updateAccountBalance($sellProduct->merchant_id, $paidShippingCost, AccountTypes::SALE->value, 'SHPC', 'credit', 'increment', $uuid);
            }

            if ($fineAmount > 0) {
                $this->updateAccountBalance($sellProduct->merchant_id, $fineAmount, AccountTypes::ASSET->value, 'SPRC', 'credit', 'decrement', $uuid);
                $this->updateAccountBalance($sellProduct->merchant_id, $fineAmount, AccountTypes::EXPENSE->value, 'SHFI', 'debit', 'increment', $uuid);
            }

            return ApiResponse::success('Shipping fee updated successfully.', $sellProduct, Response::HTTP_OK);
        } catch (ModelNotFoundException) {
            return ApiResponse::failure('Sell info not found', Response::HTTP_NOT_FOUND);
        } catch (ValidationException $v) {
            return ApiResponse::validationError('There were validation errors.', $v->validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Exception $e) {
            return ApiResponse::failure('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
            'uuid'        => $uuid,
            'merchant_id' => $merchantId,
            'account_id'  => $account->id,
            'amount'      => $method === 'increment' ? $amount : -$amount,
            'date'        => now(),
            'type'        => $type,
        ]);
    }

    public function sellUpdate(Request $request, int $id): JsonResponse
    {
        DB::beginTransaction();

        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'customer_id'                 => 'required|integer',
                    'invoice_no'                  => 'nullable|integer',
                    'sale_date'                   => 'nullable|integer',
                    'due_date'                    => 'nullable|integer',
                    'total_item'                  => 'nullable|integer',
                    'total_discount_percentage'   => 'nullable|numeric',
                    'total_discount_amount'       => 'nullable|numeric',
                    'total_sale_vat_percentage'   => 'nullable|numeric',
                    'total_sale_vat_amount'       => 'nullable|numeric',
                    'total_shipping_cost'         => 'nullable|numeric|max:20000',
                    'total_amount'                => 'nullable|numeric',
                    'grand_total'                 => 'nullable|numeric',
                    'product_discount_percentage' => 'nullable|numeric',
                    'sub_total'                   => 'nullable|numeric',
                    'products'                    => 'required|json',
                    'sold_from'                   => 'required|string|in:regular,pos',
                ],
                [
                    'total_shipping_cost.max' => 'Shipping cost is too much',
                ],
            );

            if ($validator->fails()) {
                return ApiResponse::validationError('Validation Error', $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            // request merge
            $request->merge([
                'merchant_id' => $request->user()->merchant->id,
            ]);

            $customer = Customer::where('id', $request->customer_id)->where('merchant_id', $request->merchant_id)->first();

            if (! $customer) {
                return ApiResponse::failure('Customer not found', Response::HTTP_NOT_FOUND);
            }

            $saleProduct = $this->sellProductService->createSaleProduct($request);

            if ($request->hasFile('attachment')) {
                $saleProduct->addMedia($request->file('attachment'), 'attachment');
            }

            $products = json_decode($request->products, true);

            $this->sellProductService->processProducts($products, $saleProduct, $request);

            $this->sellProductService->handleTransaction($saleProduct);

            DB::commit();

            return ApiResponse::success('Sale created successfully', [
                'id' => $saleProduct->id,
            ], Response::HTTP_CREATED);
        } catch (ValidationException $e) {
            DB::rollBack();

            return ApiResponse::validationError('Validation Error', $e->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Exception $e) {
            DB::rollBack();

            return ApiResponse::failure('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
