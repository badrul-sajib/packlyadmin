<?php

namespace Modules\Api\V1\Merchant\MerchantOrder\Http\Controllers;

use Exception;
use Throwable;
use App\Enums\CancelBy;
use App\Models\User\User;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use Illuminate\Http\Request;
use App\Services\ApiResponse;
use App\Models\Product\Product;
use App\Models\Sell\SellProduct;
use App\Models\Shop\ShopSetting;
use App\Models\Stock\StockOrder;
use App\Models\Customer\Customer;
use Illuminate\Http\JsonResponse;
use App\Models\Order\OrderPayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Stock\StockInventory;
use App\Services\InsideDhakaService;
use App\Services\SellProductService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\Merchant\MerchantOrder;
use App\Models\Sell\SellProductDetail;
use App\Models\Product\ProductVariation;
use App\Exceptions\InsufficientException;
use Symfony\Component\HttpFoundation\Response;
use Modules\Api\V1\Merchant\MerchantOrder\Http\Requests\AddressUpdateRequest;
use Modules\Api\V1\Merchant\MerchantOrder\Http\Resources\MerchantOrderResource;
use Illuminate\Support\Str;

class MerchantOrderController extends Controller
{
    protected SellProductService $sellProductService;

    public function __construct(SellProductService $service)
    {
        $this->sellProductService = $service;
        $this->middleware('shop.permission:show-merchant-orders')->only('index', 'show');
        $this->middleware('shop.permission:update-merchant-order-status')->only('orderStatusChange', 'bulkOrderStatusChange');
        $this->middleware('shop.permission:update-merchant-order-address')->only('orderAddressUpdate');
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 10);

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $merchantOrders = MerchantOrder::query()
            ->whereHas('order', function ($query) {
                $query->notSpam();
            })
            ->where('merchant_id', Auth::user()->merchant?->id)
            ->when($startDate && $endDate && $startDate == $endDate, function ($query) use ($startDate) {
                $query->whereDate('created_at', $startDate);
            })
            ->when($startDate && $endDate && $startDate != $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->when($request->has('status_id'), function ($query) use ($request) {
                $query->where('status_id', $request->input('status_id'));
            })
            ->when($request->has('tracking_id'), function ($query) use ($request) {
                $query->where('tracking_id', $request->input('tracking_id'));
            })
            ->when($request->has('transaction_id'), function ($query) use ($request) {
                $query->where('transaction_id', $request->input('transaction_id'));
            })
            ->when($request->has('search'), function ($query) use ($request) {
                $query->where(function ($query) use ($request) {
                    $query
                        ->whereHas('order', function ($orderQuery) use ($request) {
                            $orderQuery->where('customer_name', 'like', '%' . $request->input('search') . '%');
                            $orderQuery->orWhere('customer_number', 'like', '%' . $request->input('search') . '%');
                        })
                        ->orWhere('tracking_id', 'like', '%' . $request->input('search') . '%')
                        ->orWhere('invoice_id', 'like', '%' . $request->input('search') . '%')
                        ->orWhereHas('payment', function ($paymentQuery) use ($request) {
                            $paymentQuery->where('tran_id', 'like', '%' . $request->input('search') . '%');
                        });
                });
            })
            ->select(['id', 'invoice_id', 'tracking_id', 'order_id', 'merchant_id', 'total_amount', 'sub_total', 'grand_total', 'discount_amount', 'shipping_amount', 'status_id', 'created_at', 'consignment_id', 'courier_status'])
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        $allStatusIds = array_map(fn($case) => $case->value, OrderStatus::cases());

        $statusCounts = MerchantOrder::where('merchant_id', Auth::user()->merchant?->id)
            ->selectRaw('status_id, count(*) as count')
            ->groupBy('status_id')
            ->pluck('count', 'status_id');

        $statusCountsWithZero = collect($allStatusIds)->mapWithKeys(function ($statusId) use ($statusCounts) {
            return [$statusId => $statusCounts->get($statusId, 0)];
        });

        return ApiResponse::formatPagination('All Merchant Orders', MerchantOrderResource::collection($merchantOrders), Response::HTTP_OK, $statusCountsWithZero);
    }

    public function show($merchantOrder): JsonResponse
    {
        $merchantOrder = MerchantOrder::where('id', $merchantOrder)
            ->with(['items.product', 'items.product_variant', 'items.itemCase.reason'])
            ->first();
        // status is_seen
        $merchantOrder->is_seen = 1;
        $merchantOrder->save();
        return ApiResponse::success('Merchant Orders Details', MerchantOrderResource::make($merchantOrder), Response::HTTP_OK);
    }


    public function orderStatusChange(MerchantOrder $merchantOrder, int $status): JsonResponse
    {
        if ($this->isPayoutLocked($merchantOrder)) {
            return $this->payoutLockFailureResponse();
        }

        $currentStatus = $merchantOrder->status_id->value;

        if ($currentStatus == $status) {
            return ApiResponse::failure(
                'Order already in this status',
                Response::HTTP_BAD_REQUEST
            );
        }

        if (!$this->validateStatus($currentStatus, $status)) {

            $currentLabel = OrderStatus::getStatusLabel($currentStatus);
            $updatedLabel = OrderStatus::getStatusLabel($status);

            return ApiResponse::failure(
                "{$currentLabel} status can't be updated to {$updatedLabel}",
                Response::HTTP_BAD_REQUEST
            );
        }

        try {

            DB::beginTransaction();

            $merchantOrder = MerchantOrder::lockForUpdate()->findOrFail($merchantOrder->id);
            $order = $merchantOrder->order;

            /**
             * Create sell product + decrease stock
             * only when order moves from PENDING
             */
            if (
                $merchantOrder->status_id->value == OrderStatus::PENDING->value &&
                in_array($status, [
                    OrderStatus::PROCESSING->value,
                    OrderStatus::READY_TO_SHIP->value,
                    OrderStatus::APPROVED->value
                ])
            ) {
                $this->sellProductCreate($merchantOrder);
                $this->decreaseStock($merchantOrder);
            }

            /**
             * Restore stock when cancelled / returned
             */
            switch ($status) {

                case OrderStatus::CANCELLED->value:

                    $merchantOrder->cancel_by = CancelBy::MERCHANT->value;

                    $this->sellProductDelete($merchantOrder);
                    $this->increaseStock($merchantOrder);

                    break;

                case OrderStatus::RETURNED->value:

                    $this->sellProductDelete($merchantOrder);
                    $this->increaseStock($merchantOrder);

                    break;
            }

            /**
             * Update merchant order status
             */
            $merchantOrder->status_id = $status;
            $merchantOrder->save();

            /**
             * Update order item statuses
             */
            $merchantOrder->items()
                ->whereNotIn('status_id', [
                    OrderStatus::CANCELLED->value,
                    OrderStatus::RETURNED->value,
                    OrderStatus::REFUNDED->value
                ])
                ->update([
                    'status_id' => $status,
                    'action_by' => Auth::id()
                ]);

            /**
             * Update timeline
             */
            $timeline = $merchantOrder->orderTimeLines()
                ->where('status_id', $status)
                ->first();

            if ($timeline) {
                $timeline->update(['date' => now()]);
            }

            /**
             * Courier creation when READY_TO_SHIP
             */
            if ($status == OrderStatus::READY_TO_SHIP->value) {

                $sfcConfig = cache()->remember('sfc_config', 3600, function () {
                    return ShopSetting::whereIn('key', [
                        'sfc_base_url',
                        'sfc_public_key',
                        'sfc_secret_key'
                    ])->pluck('value', 'key')->toArray();
                });

                if (
                    empty($sfcConfig['sfc_base_url']) ||
                    empty($sfcConfig['sfc_public_key']) ||
                    empty($sfcConfig['sfc_secret_key'])
                ) {
                    DB::rollBack();

                    return ApiResponse::failure(
                        'Courier configuration problem',
                        Response::HTTP_BAD_REQUEST
                    );
                }

                $itemDescription = $merchantOrder->items()
                    ->whereNotIn('status_id', [
                        OrderStatus::CANCELLED->value,
                        OrderStatus::RETURN_REQUEST->value,
                        OrderStatus::RETURNED->value,
                        OrderStatus::REFUNDED->value,
                        OrderStatus::UNKNOWN->value
                    ])
                    ->with('product:id,name')
                    ->get()
                    ->map(fn($item) => optional($item->product)->name)
                    ->filter()
                    ->implode(', ');

                $apiBodyParams = [
                    'invoice' => $merchantOrder->invoice_id ?? $order->invoice_id,
                    'recipient_name' => $order->customer_name,
                    'recipient_phone' => $order->customer_number,
                    'recipient_address' => $order->customer_address,
                    'cod_amount' => $merchantOrder->codAmount(),
                    'item_description' => Str::limit($itemDescription, 200, ''),
                    'note' => $merchantOrder->notes,
                ];

                $sfcData = (object) $sfcConfig;

                $consignmentId = $this->createOrderOnSteadfast($apiBodyParams, $sfcData);

                if (!$consignmentId) {

                    DB::rollBack();

                    return ApiResponse::failure(
                        'Courier order creation failed',
                        Response::HTTP_BAD_REQUEST
                    );
                }

                $merchantOrder->consignment_id = $consignmentId;
                $merchantOrder->save();
            }

            /**
             * Update order payment status
             */
            $this->updateOrderPaymentStatus($merchantOrder);

            DB::commit();

            /**
             * Activity log
             */
            activity()
                ->performedOn($merchantOrder)
                ->useLog('merchant_order_status_change')
                ->withProperties([
                    'status' => OrderStatus::getStatusLabel($status),
                    'changed_by' => optional($merchantOrder->merchant)->name,
                    'changed_at' => now()
                ])
                ->log('Merchant Order Status changed');

            return ApiResponse::success(
                'Merchant Order Status Updated Successfully',
                MerchantOrderResource::make($merchantOrder->fresh()),
                Response::HTTP_OK
            );

        } catch (InsufficientException $e) {

            DB::rollBack();

            return ApiResponse::failure(
                $e->getMessage(),
                Response::HTTP_BAD_REQUEST
            );

        } catch (Exception $e) {

            DB::rollBack();

            return ApiResponse::failure(
                $e->getMessage(),
                Response::HTTP_BAD_REQUEST
            );

        } catch (Throwable $e) {

            DB::rollBack();

            Log::error('Merchant Order Status Update Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return ApiResponse::failure(
                'Something went wrong',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }


    /**
     * @throws Throwable
     */
    public function bulkOrderStatusChange(Request $request): JsonResponse
    {
        $requestJson = $request->json()->all();

        if (!is_array($requestJson['orders']) || empty($requestJson['orders'])) {
            return ApiResponse::failure('Invalid or empty orders data provided', Response::HTTP_NOT_FOUND);
        }

        $orders = $requestJson['orders'];
        $results = [];
        $hasFailures = false;

        DB::beginTransaction();
        try {
            foreach ($orders as $order) {
                if (!isset($order['id']) || !isset($order['status_id'])) {
                    $results[] = [
                        'id' => $order['id'] ?? 'unknown',
                        'success' => false,
                        'message' => 'Missing required fields (id or status_id)',
                    ];
                    $hasFailures = true;

                    continue;
                }

                $merchantOrder = MerchantOrder::find($order['id']);
                if (!$merchantOrder) {
                    $results[] = [
                        'id' => $order['id'],
                        'success' => false,
                        'message' => 'Merchant order not found',
                    ];
                    $hasFailures = true;

                    continue;
                }

                $response = $this->orderStatusChange($merchantOrder, $order['status_id']);

                $responseData = json_decode($response->getContent(), true);

                $results[] = [
                    'id' => $order['id'],
                    'success' => $responseData['status'] === 'success',
                    'message' => $responseData['message'],
                ];

                if ($responseData['status'] !== 'success') {
                    $hasFailures = true;
                }
            }

            if ($hasFailures && count($results) > 0) {
                DB::rollBack();

                return ApiResponse::failure('Some orders failed to update', Response::HTTP_UNPROCESSABLE_ENTITY, ['results' => $results]);
            }

            DB::commit();

            return ApiResponse::success('All orders updated successfully', ['results' => $results], Response::HTTP_OK);
        } catch (Throwable $th) {
            DB::rollBack();

            return ApiResponse::failure('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function sellProductCreate(MerchantOrder $merchantOrder)
    {
        $user = $merchantOrder->order->user;

        $customer = $this->createCustomer((object) [
            'merchant_id' => $merchantOrder->merchant_id,
            'name' => $merchantOrder->order->customer_name,
            'email' => $user->email ?? null,
            'phone' => $user->phone ?? null,
            'address' => $merchantOrder->order->customer_address,
            'customer_type_id' => 1,
        ]);

        if (!$customer) {
            throw new Exception('Customer creation failed.');
        }

        $saleProduct = SellProduct::firstOrCreate(
            [
                'order_id' => $merchantOrder->order_id,
                'merchant_id' => $merchantOrder->merchant_id
            ],
            [
                'customer_id' => $customer->id,
                'invoice_no' => $this->sellProductService->generateUniqueInvoiceNo(),
                'sale_date' => $merchantOrder->getRawOriginal('created_at'),
                'due_date' => $merchantOrder->getRawOriginal('created_at'),
                'total_item' => $merchantOrder->items->sum('quantity'),
                'sold_from' => 'Ecommerce',
            ]
        );

        foreach ($merchantOrder->items as $item) {

            SellProductDetail::updateOrCreate(
                [
                    'sell_product_id' => $saleProduct->id,
                    'product_id' => $item->product_id,
                    'variation_id' => $item->product_variation_id,
                ],
                [
                    'sale_qty' => $item->quantity,
                    'unit_cost' => $item->price,
                    'sub_total' => $item->price * $item->quantity,
                ]
            );
        }

        $subtotal = $merchantOrder->sub_total;

        $saleProduct->update([
            'total_discount_amount' => $merchantOrder->discount_amount,
            'total_discount_percentage' => $this->sellProductService
                ->getPercentageValue($merchantOrder->discount_amount, $subtotal),
            'total_sale_vat_percentage' => 0,
            'total_sale_vat_amount' => 0,
            'total_shipping_cost' => 0,
            'total_amount' => $subtotal,
            'grand_total' => $subtotal - $merchantOrder->discount_amount,
        ]);
    }

    private function sellProductDelete(MerchantOrder $merchantOrder)
    {
        $sellProduct = SellProduct::where('order_id', $merchantOrder->order_id)->first();
        $sellProduct?->delete();
    }

    private function createCustomer($data)
    {
        $customer = Customer::where('email', $data->email)->orWhere('phone', $data->phone)->first();

        if ($customer) {
            return $customer;
        }

        return Customer::create([
            'merchant_id' => $data->merchant_id,
            'name' => $data->name,
            'email' => $data->email ?? null,
            'phone' => $data->phone ?? null,
            'address' => $data->address,
            'customer_type_id' => $data->customer_type_id,
        ]);
    }

    /**
     * @throws Exception
     */
    public function decreaseStock(MerchantOrder $merchantOrder)
    {
        foreach ($merchantOrder->items as $orderItem) {

            $isVariation = !is_null($orderItem->product_variation_id);

            $stockInventories = StockInventory::where('merchant_id', $merchantOrder->merchant_id)
                ->when(
                    $isVariation,
                    fn($q) => $q->where('product_variation_id', $orderItem->product_variation_id),
                    fn($q) => $q->where('product_id', $orderItem->product_id)
                )
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            if ($stockInventories->isEmpty()) {
                throw new Exception('Product is out of stock.');
            }

            $remainingQty = $orderItem->quantity;

            foreach ($stockInventories as $inventory) {

                if ($remainingQty <= 0)
                    break;

                $stockOrders = StockOrder::where('stock_inventory_id', $inventory->id)
                    ->whereNull('sell_product_detail_id')
                    ->orderBy('id')
                    ->limit($remainingQty)
                    ->lockForUpdate()
                    ->get();

                foreach ($stockOrders as $stockOrder) {

                    $stockOrder->update([
                        'type' => 2,
                        'sell_product_detail_id' => $orderItem->id,
                    ]);

                    $remainingQty--;
                }
            }

            if ($remainingQty > 0) {
                throw new InsufficientException('Not enough stock available across all inventories.');
            }

            Product::where('id', $orderItem->product_id)
                ->decrement('total_stock_qty', $orderItem->quantity);

            if ($isVariation) {
                ProductVariation::where('id', $orderItem->product_variation_id)
                    ->decrement('total_stock_qty', $orderItem->quantity);
            }
        }
    }

    public function increaseStock(MerchantOrder $merchantOrder)
    {
        foreach ($merchantOrder->items as $item) {

            Product::where('id', $item->product_id)
                ->increment('total_stock_qty', $item->quantity);

            if ($item->product_variation_id) {

                ProductVariation::where('id', $item->product_variation_id)
                    ->increment('total_stock_qty', $item->quantity);
            }

            StockOrder::where([
                'type' => 2,
                'sell_product_detail_id' => $item->id,
            ])->update([
                        'sell_product_detail_id' => null,
                        'type' => null
                    ]);
        }
    }

    private function createOrderOnSteadfast($apiBodyParams, $courier)
    {
        $apiUrl = $courier->sfc_base_url . '/create_order';

        $headers = [
            'api-key' => $courier->sfc_public_key,
            'secret-key' => $courier->sfc_secret_key,
        ];

        $response = Http::withHeaders($headers)->post($apiUrl, $apiBodyParams);

        if (!$response->successful()) {
            return null;
        }

        $consignmentId = data_get($response->json(), 'consignment.consignment_id');

        if (empty($consignmentId)) {
            Log::warning('Steadfast create_order response missing consignment id', [
                'response' => $response->json(),
            ]);

            return null;
        }


        return $consignmentId;
    }

    public function updateOrderPaymentStatus($merchantOrder)
    {
        if ($merchantOrder->payment?->payment_method == 'COD') {
            if ($merchantOrder->status_id->value == OrderStatus::DELIVERED->value) {
                $merchantOrder->payment->payment_status = OrderPayment::$PAID;
                $merchantOrder->payment->save();
            }
        }
    }

    // check incoming status
    private function validateStatus($merchantOrderStatus, $status)
    {
        if ($merchantOrderStatus === $status) {
            return false;
        }

        switch ($merchantOrderStatus) {
            case OrderStatus::PENDING->value:
                if (!in_array($status, [OrderStatus::APPROVED->value, OrderStatus::PROCESSING->value, OrderStatus::READY_TO_SHIP->value, OrderStatus::CANCELLED->value])) {
                    return false;
                }

                break;

            case OrderStatus::APPROVED->value:
                if (!in_array($status, [OrderStatus::PROCESSING->value, OrderStatus::READY_TO_SHIP->value, OrderStatus::CANCELLED->value, OrderStatus::RETURNED->value])) {
                    return false;
                }

                break;

            case OrderStatus::PROCESSING->value:
                if (!in_array($status, [OrderStatus::READY_TO_SHIP->value, OrderStatus::CANCELLED->value, OrderStatus::RETURNED->value])) {
                    return false;
                }

                break;

            case OrderStatus::DELIVERED->value:
                return false;

            case OrderStatus::RETURN_REQUEST->value:
                if ($status !== OrderStatus::RETURNED->value) {
                    return false;
                }

                break;

            case OrderStatus::RETURNED->value:
                if ($status !== OrderStatus::REFUNDED->value) {
                    return false;
                }

                break;

            case OrderStatus::CANCELLED->value:
                if ($status != OrderStatus::PENDING->value) {
                    return false;
                }
                break;

            case OrderStatus::REFUNDED->value:
                return false;

            default:
                return false;
        }

        return true;
    }

    /**
     * @throws Throwable
     */
    public function orderAddressUpdate(AddressUpdateRequest $request, MerchantOrder $merchantOrder): JsonResponse
    {
        if ($this->isPayoutLocked($merchantOrder)) {
            return $this->payoutLockFailureResponse();
        }

        DB::beginTransaction();

        try {
            $order = $merchantOrder->order;

            if (!in_array($merchantOrder->status_id->value, [OrderStatus::PENDING->value, OrderStatus::APPROVED->value, OrderStatus::PROCESSING->value])) {
                return ApiResponse::failure('Order status should be pending, approved or processing', Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $isInsideDhaka = $this->isInsideDhaka($request->address);

            $getDeliveryCharges = $merchantOrder->merchant->getDeliveryCharges();

            $oldTotalShipping = $order->total_shipping_fee;
            $totalDeliveryCharge = 0;

            foreach ($order->merchantOrders as $mo) {
                $totalMerchantDeliveryCharge = $mo->items->sum(function ($item) use ($isInsideDhaka, $getDeliveryCharges) {
                    return $isInsideDhaka ? $getDeliveryCharges['id_delivery_fee'] : $getDeliveryCharges['od_delivery_fee'];
                });

                $oldMerchantShipping = $mo->shipping_amount;

                $mo->shipping_amount = $totalMerchantDeliveryCharge;
                $mo->grand_total = $mo->grand_total + ($totalMerchantDeliveryCharge - $oldMerchantShipping);
                $mo->save();

                $totalDeliveryCharge += $totalMerchantDeliveryCharge;
            }

            $order->customer_address = $request->address;
            $order->order_address_edited_by = auth()->user()->merchant->id;
            $order->shipping_type = $isInsideDhaka ? 'ISD' : 'OSD';
            $order->total_shipping_fee = $totalDeliveryCharge;
            $order->grand_total = $order->grand_total + ($totalDeliveryCharge - $oldTotalShipping);
            $order->save();

            DB::commit();

            return ApiResponse::success('Order Address Updated', [], Response::HTTP_OK);
        } catch (Exception $e) {
            DB::rollBack();

            return ApiResponse::error('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function isInsideDhaka($address): bool
    {
        if (preg_match('/[\x80-\xff]/', $address)) {
            $address = banglaToBanglish($address);
        }

        return (new InsideDhakaService)->isInsideDhaka($address);
    }

    public function orderTracking(string $invoiceNumber): JsonResponse
    {
        try {

            $sfcConfig = ShopSetting::whereIn('key', ['sfc_base_url', 'sfc_public_key', 'sfc_secret_key'])->pluck('value', 'key')->toArray();

            if (empty($sfcConfig['sfc_base_url']) || empty($sfcConfig['sfc_public_key']) || empty($sfcConfig['sfc_secret_key'])) {
                return ApiResponse::failure('SFC Configuration problem, please contact admin', Response::HTTP_NOT_FOUND);
            }

            $response = retry(3, function () use ($sfcConfig, $invoiceNumber) {
                return Http::withHeaders([
                    'api-key' => $sfcConfig['sfc_public_key'],
                    'secret-key' => $sfcConfig['sfc_secret_key'],
                ])->get($sfcConfig['sfc_base_url'] . '/trackings_by_invoice/' . $invoiceNumber);
            }, 1000)->throw();


            if (!$response->successful()) {
                return ApiResponse::error('Failed to retrieve order tracking by invoice number ' . $invoiceNumber, Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $data = $response->json();

            return ApiResponse::success(
                'Order tracking retrieved successfully.',
                [
                    'tracking' => $data['tracking'] ?? [],
                ],
                Response::HTTP_OK
            );
        } catch (Exception $e) {
            return ApiResponse::error('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function fraudChecker(string $phoneNumber): JsonResponse
    {
        $sfcConfig = ShopSetting::whereIn('key', ['sfc_base_url', 'sfc_public_key', 'sfc_secret_key'])->pluck('value', 'key')->toArray();

        if (empty($sfcConfig['sfc_base_url']) || empty($sfcConfig['sfc_public_key']) || empty($sfcConfig['sfc_secret_key'])) {
            return ApiResponse::failure('SFC Configuration problem, please contact admin', Response::HTTP_NOT_FOUND);
        }

        $response = Http::withHeaders([
            'api-key' => $sfcConfig['sfc_public_key'],
            'secret-key' => $sfcConfig['sfc_secret_key'],
        ])->get($sfcConfig['sfc_base_url'] . '/fraud_check/' . $phoneNumber);

        if (!$response->successful()) {
            return ApiResponse::error('Failed to retrieve order tracking by phone number ' . $phoneNumber, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = $response->json();

        return ApiResponse::success(
            'Phone number tracking retrieved successfully.',
            $data,
            Response::HTTP_OK
        );
    }

    private function isPayoutLocked(MerchantOrder $merchantOrder): bool
    {
        return ! is_null($merchantOrder->payout_id);
    }

    private function payoutLockFailureResponse(): JsonResponse
    {
        return ApiResponse::failure(
            'This merchant order is locked because payout has already been created.',
            Response::HTTP_FORBIDDEN
        );
    }
}
