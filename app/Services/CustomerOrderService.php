<?php

namespace App\Services;


use App\Enums\CancelBy;
use App\Enums\CouponApplyOn;
use App\Caches\ShopSettingsCache;
use App\Enums\ItemStatus;
use App\Enums\ItemType;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Http\Resources\Ecommerce\OrderReturnResource;
use App\Models\Merchant\MerchantOrder;
use App\Models\Order\OrderItem;
use App\Models\Shop\ShopSetting;
use App\Support\CalculateWeightBasedCharge;
use App\Traits\Transaction;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class CustomerOrderService
{
    use Transaction;

    public static function getCustomerOrder($request): JsonResponse
    {
        $user    = userInfo();
        $status  = $request->status ?? null;
        $perPage = $request->input('per_page', 10);

        $orders = MerchantOrder::query()
            ->whereHas('order', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status_id', $status);
            })
            ->with([
                'merchant:id,name,shop_name',
                'orderItems:id,merchant_order_id,product_id,product_variation_id,price,quantity,status_id',
                'orderItems.product.media',
                'orderItems.product.reviews',
                'orderItems.product:id,name,slug',
                'orderItems.product_variant.variationAttributes.attributeOption:id,attribute_value',
                'orderItems.product_variant.variationAttributes.attribute:id,name',
                'payment',
            ])
            ->select('id', 'tracking_id', 'total_amount', 'shipping_amount', 'merchant_id', 'status_id')
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        return formatPagination('Orders fetched successfully', $orders);
    }

    public static function getCustomerOrderDetails(string $tracking_id): array
    {
        $orderReturnDays = (int) ShopSetting::where('key', 'order_return_days')->value('value') ?? 3;
        $merchantOrder   = MerchantOrder::where('tracking_id', $tracking_id)
            ->userOrders()
            ->with([
                'order:id,customer_name,customer_number,customer_landmark,customer_address,customer_location_id,total_amount,grand_total',
                'order.customer_location:id,name,type,parent_id',
                'order.customer_location.parent:id,name,type,parent_id',
                'order.customer_location.parent.parent:id,name,type,parent_id',
                'orderItems:id,merchant_order_id,product_id,product_variation_id,price,quantity,status_id',
                'orderItems.product:id,name,slug,sku',
                'orderItems.product_variant:id,sku,product_id',
                'orderItems.product_variant.variationAttributes:id,attribute_option_id,product_variation_id,product_id,attribute_id',
                'orderItems.product_variant.variationAttributes.attributeOption:id,attribute_value',
                'orderItems.product_variant.variationAttributes.attribute:id,name',
                'orderTimeLines',
            ])
            ->select('id', 'tracking_id', 'total_amount', 'shipping_amount', 'discount_amount', 'sub_total', 'merchant_id', 'status_id', 'created_at', 'order_id', 'updated_at', 'grand_total', 'total_items')
            ->first();

        $city     = $merchantOrder->order->customer_location ?? null;
        $district = $city?->parent;
        $division = $district?->parent;

        $returnDate = null;
        if ($merchantOrder?->status_id->value == OrderStatus::DELIVERED->value) {
            $returnDate = $merchantOrder->updated_at->copy()->addDays($orderReturnDays)->format('Y-m-d H:i:s');
        }

        return [
            'order'               => (int) $merchantOrder->id,
            'parent_order_id'     => $merchantOrder->order_id,
            'parent_total_amount' => (float) $merchantOrder->order?->grand_total,
            'tracking_id'         => $merchantOrder->tracking_id,
            'status_id'           => (int) $merchantOrder->status_id->value,
            'status'              => $merchantOrder->status_label,
            'status_message'      => self::getOrderStatusMessage($merchantOrder->status_label),
            'sub_total'           => $merchantOrder->sub_total,
            'shipping_amount'     => (float) $merchantOrder->shipping_amount,
            'discount_amount'     => (float) $merchantOrder->discount_amount,
            'charge'              => 0, // $merchantOrder->charge,
            'total_amount'        => (float) $merchantOrder->grand_total,
            'customer_name'       => $merchantOrder->order?->customer_name,
            'customer_number'     => $merchantOrder->order?->customer_number,
            'customer_landmark'   => $merchantOrder->order?->customer_landmark,
            'customer_address'    => $merchantOrder->order?->customer_address,
            'division'            => $division?->name,
            'district'            => $district?->name,
            'city'                => $city?->name,
            'created_at'          => $merchantOrder->created_at->format('Y-m-d H:i:s'),
            'shop_id'             => (int) $merchantOrder->merchant->id,
            'shop_slug'           => $merchantOrder->merchant?->slug,
            'shop_name'           => $merchantOrder->merchant?->shop_name,
            'shop_image'          => $merchantOrder->merchant?->shop_logo ?? null,
            'total_items_count'   => $merchantOrder->total_items,
            'return_date'         => $returnDate,
            'timeline'            => $merchantOrder->orderTimeLines?->whereNotNull('date')?->map(function ($timeline) {
                return [
                    'id'        => (int) $timeline->id,
                    'status_id' => (int) $timeline->status_id,
                    'status'    => OrderStatus::getStatusLabels()[$timeline->status_id] ?? 'Unknown',
                    'message'   => $timeline->message,
                    'date'      => $timeline->date,
                ];
            })->values(),
            'order_items'         => $merchantOrder->orderItems?->map(function ($item) use ($merchantOrder, $returnDate, $orderReturnDays) {

                $thumbnail = $item->product?->thumbnail;

                if (isset($item->product_variant) and $item->product_variant?->image) {
                    $thumbnail = $item->product_variant?->image;
                }
                $returnable = ! (($merchantOrder->status_id->value == OrderStatus::DELIVERED->value && Carbon::now()->diffInDays($merchantOrder->updated_at) > $orderReturnDays));

                return [
                    'id'                => (int) $item->id,
                    'price'             => (int) $item->price,
                    'quantity'          => (int) $item->quantity,
                    'product_name'      => $item->product?->name,
                    'product_thumbnail' => $thumbnail ?? '',
                    'product_slug'      => $item->product?->slug,
                    'product_sku'       => $item->product?->sku,
                    'status'            => $item->status_label,
                    'is_reviewed'       => (bool) $item->review,
                    'product_variant'   => OrderService::getOrderItemVariantText($item->product_variant?->variations ?? []),
                    'returnable'        => $returnable,
                    'is_delivered'      => in_array($merchantOrder->status_id->value, [
                        OrderStatus::DELIVERED->value,
                        OrderStatus::RETURN_REQUEST->value,
                        OrderStatus::RETURNED->value,
                        OrderStatus::REFUNDED->value,
                    ]),
                    'is_returned'       => $item->status_id == OrderStatus::RETURNED->value,
                    'return_date'       => $returnDate,
                ];
            }),
            'payment_method' => $merchantOrder->payment?->payment_method,
            'payment_status' => $merchantOrder->payment?->status_label,
        ];
    }

    /**
     * @throws Throwable
     */
    public static function OrderItemCancel(array $data, string $tracking_id): JsonResponse
    {
        DB::beginTransaction();

        try {
            $ids = $data['item_ids'];

            // Fetch the Merchant Order and validate it
            $merchantOrder = MerchantOrder::where('tracking_id', $tracking_id)->userOrders()->firstOrFail();
            $order         = $merchantOrder->order;
            if (! $order) {
                throw new Exception('Order not found for the given tracking ID.');
            }

            // Fetch the relevant Order Items
            $orderItems = $merchantOrder->orderItems()->where('status_id', OrderStatus::PENDING->value)->whereIn('id', $ids)->get();

            if ($orderItems->isEmpty()) {
                throw new Exception('No pending items found for cancellation.');
            }

            $shippingSettings =  ShopSettingsCache::select(
                'shipping_fee_osd',
                'shipping_fee_isd',
                'shipping_additional_fee_isd',
                'shipping_additional_fee_osd',
            );

            // Update the status for the fetched items and create ItemCase entries
            $orderItems->each(function ($item) use ($data, $order, $merchantOrder, $shippingSettings) {

                $item->update(['status_id' => OrderStatus::CANCELLED->value]);
                $weight = $item->weight ?? 0;

                $item->itemCase()->create([
                    'reason_id' => $data['reason_id'],
                    'status'    => ItemStatus::APPROVED->value,
                    'type'      => ItemType::CANCELLED->value,
                ]);

                $currentMerchantShippingAmount = $merchantOrder->shipping_amount;

                // $newMerchantShippingPrice = CustomerOrderService::recalculateShipping($merchantOrder, $order->shipping_type);
                $itemRegularPrice         = $item->regular_price * $item->quantity;
                $itemDiscountPrice        = $item->price         * $item->quantity;
                $itemCouponDiscount       = $item->coupon_discount ?? 0;
                $cancelQuantity           = $item->quantity;

                // First calculate updated merchant order values
                $updatedMerchantTotalAmount   = $merchantOrder->total_amount                         - $itemRegularPrice;
                $updatedMerchantSubTotal      = $merchantOrder->sub_total                            - $itemDiscountPrice;
                $updatedMerchantItemDiscount  = $updatedMerchantTotalAmount                          - $updatedMerchantSubTotal;
                $latestMerchantDiscountAmount = $merchantOrder->discount_amount                      - $itemCouponDiscount;
                $totalWeight                  = $merchantOrder->total_weight                         - $weight;

                // There are not free shipping calculation yet it should be implement in future
                $newMerchantShippingPrice   = CalculateWeightBasedCharge::run(
                    totalWeight: $totalWeight,
                    shippingType: $order->shipping_type,
                    isd_fee: $shippingSettings->shipping_fee_isd,
                    osd_fee: $shippingSettings->shipping_fee_osd,
                    additional_isd_fee: $shippingSettings->shipping_additional_fee_isd,
                    additional_osd_fee: $shippingSettings->shipping_additional_fee_osd,
                );

                $merchantOrder->update([
                    'total_items'     => $merchantOrder->total_items - $cancelQuantity,
                    'total_amount'    => $updatedMerchantTotalAmount, // only product price with regular_price
                    'sub_total'       => $updatedMerchantSubTotal,  // only product price with discounted_price
                    'item_discount'   => $updatedMerchantItemDiscount,
                    'discount_amount' => $merchantOrder->discount_amount - $itemCouponDiscount, // coupon and other discounts
                    'shipping_amount' => $newMerchantShippingPrice,
                    'total_weight'    => $totalWeight,
                    'grand_total'     => $updatedMerchantSubTotal + $newMerchantShippingPrice - $latestMerchantDiscountAmount, // total amount with shipping and discount
                ]);

                // Update the order total price and total amount
                $updatedOrderTotalAmount   = $order->total_amount       - $itemRegularPrice;
                $updatedOrderSubTotal      = $order->sub_total          - $itemDiscountPrice;
                $updatedOrderItemDiscount  = $updatedOrderTotalAmount   - $updatedOrderSubTotal;
                $latestOrderDiscountAmount = $order->total_discount     - $itemCouponDiscount;  
                $new_total_shipping_fee    = $order->total_shipping_fee;
                

                
                if($order->couponUsage && $order->couponUsage->coupon_type == CouponApplyOn::SHIPPING_CHARGE->value){
    
                    if($order->couponUsage->min_purchase > $updatedOrderSubTotal) {

                        $discount_amount = 0;

                        if($order->couponUsage->discount_type == 'percentage' ) {
                            $discount_amount = ($order->couponUsage->discount_amount * $updatedOrderSubTotal) / 100;

                            if($order->couponUsage->max_discount && $discount_amount > $order->couponUsage->max_discount) {
                                $discount_amount = $order->couponUsage->max_discount;
                            }
                        } else {
                            $discount_amount = $order->couponUsage->discount_amount;
                        }

                        $new_total_shipping_fee += $discount_amount;
                    }
                }
                
                $newMainOrderShippingPrice = max(0,$new_total_shipping_fee - $currentMerchantShippingAmount + $newMerchantShippingPrice); 


                $order->update([
                    'total_items'        => $order->total_items - $cancelQuantity,
                    'total_amount'       => $updatedOrderTotalAmount, // only product price with regular_price
                    'sub_total'          => $updatedOrderSubTotal,  // only product price with discounted_price
                    'item_discount'      => $updatedOrderItemDiscount,
                    'total_discount'     => $order->total_discount - $itemCouponDiscount, // coupon and other discounts
                    'total_shipping_fee' => $newMainOrderShippingPrice,
                    'grand_total'        => $updatedOrderSubTotal + $newMainOrderShippingPrice - $latestOrderDiscountAmount, // total amount with shipping and discount
                ]);
            });

            // Check if all items in the merchant order are now cancelled
            $remainingItemsCount = $merchantOrder->orderItems()
                ->where('status_id', '!=', OrderStatus::CANCELLED->value)
                ->count();

            if ($remainingItemsCount == 0) {
                $merchantOrder->update(['status_id' => OrderStatus::CANCELLED->value, 'cancel_by' => CancelBy::CUSTOMER->value]);
                if ($merchantOrder->payment?->payment_status == PaymentStatus::PENDING->value) {
                    $merchantOrder->payment->update(['payment_status' => PaymentStatus::CANCELLED->value]);
                }
                if ($order->merchantOrders()->count() == 1) {
                    $order->update(['status_id' => OrderStatus::CANCELLED->value]);
                }
            }

            DB::commit();

            return success('Items cancelled successfully.', 204);
        } catch (Exception $e) {
            DB::rollBack();

            return failure('Failed to cancel items.', 500);
        }
    }

    /**
     * @throws Throwable
     */
    public static function OrderItemReturn(array $data, string $tracking_id): JsonResponse
    {
        // if order created_at is greater than 3  days user can not return the product
        $order           = MerchantOrder::where('tracking_id', $tracking_id)->userOrders()->first();
        $updatedAt       = $order->updated_at;
        $orderReturnDays = (int) ShopSetting::where('key', 'order_return_days')->value('value') ?? 3;

        if (Carbon::now()->diffInDays($updatedAt) > $orderReturnDays) {
            $message = "You can not return the product after {$orderReturnDays} days";

            return failure($message);
        }

        DB::beginTransaction();

        try {
            // Fetch the items first
            $orderItems = OrderItem::find($data['item_id']);
            if ($orderItems->status_id == OrderStatus::RETURNED->value) {
                return failure('Item already returned');
            }

            $orderItems->update([
                'status_id' => OrderStatus::RETURNED->value,
            ]);

            $item = $orderItems->itemCase()->create([
                'reason_id' => $data['reason_id'],
                'note'      => $data['note'],
                'status'    => ItemStatus::PENDING->value,
                'type'      => ItemType::RETURNED->value,
            ]);

            $item->images = $data['images'] ?? '';
            $item->save();

            DB::commit();

            try {
                $notification = 'You have a new return request';
                $orderItems->merchant->merchant->sendNotification('Return Request', $notification, '/return-list');
            } catch (Throwable $th) {
                Log::error($th->getMessage());
            }

            return success('Item return request successfully', $item, 201);
        } catch (Exception $e) {
            DB::rollback();

            return failure('Failed to return item', 500);
        }
    }

    public static function getCustomerReturns(): JsonResponse
    {
        $status     = OrderStatus::RETURNED->value;
        $orderItems = OrderItem::with([
            'merchant:id,order_id,tracking_id,total_amount,shipping_amount,merchant_id,status_id,created_at',
            'merchant.order:id,user_id',
            'merchant.merchant:id,shop_name,slug',
            'product:id,name,slug,sku',
            'product_variant:id,sku',
            'product_variant.variationAttributes:id,attribute_option_id,product_variation_id,product_id,attribute_id',
            'product_variant.variationAttributes.attributeOption:id,attribute_value',
            'product_variant.variationAttributes.attribute:id,name',
            'itemCase:id,order_item_id,status,updated_at',
        ])
            ->where('status_id', $status)
            ->whereHas('merchant', function ($query) {
                $query->whereHas('order', function ($q) {
                    $q->where('user_id', auth()->user()->id);
                });
            })->latest()->paginate();

        return resourceFormatPagination('Customer returns', OrderReturnResource::collection($orderItems->items()), $orderItems);
    }

    public static function getCustomerReturnDetails($item_id, $tracking_id): JsonResponse
    {

        $merchantOrder = MerchantOrder::where('tracking_id', $tracking_id)
            ->userOrders()
            ->with([
                'order:id,user_id,customer_name,customer_number,customer_landmark,customer_address,customer_location_id',
                'order.customer_location:id,name,type,parent_id',
                'order.customer_location.parent:id,name,type,parent_id',
                'order.customer_location.parent.parent:id,name,type,parent_id',
                'orderItems:id,merchant_order_id,product_id,product_variation_id,price,quantity,status_id,item_final_price',
                'orderItems.product:id,name,slug,sku',
                'orderItems.product_variant:id,sku,product_id',
                'orderItems.product_variant.variationAttributes:id,attribute_option_id,product_variation_id,product_id,attribute_id',
                'orderItems.product_variant.variationAttributes.attributeOption:id,attribute_value',
                'orderItems.product_variant.variationAttributes.attribute:id,name',
                'orderItems' => function ($query) {
                    $query->where('status_id', OrderStatus::RETURNED->value);
                },
            ])
            ->select('id', 'tracking_id', 'total_amount', 'shipping_amount', 'discount_amount', 'sub_total', 'merchant_id', 'status_id', 'order_id', 'return_request_id', 'created_at')
            ->first();

        $order = $merchantOrder?->order;

        if (! $order) {
            return failure('Order not found for the given tracking ID.', Response::HTTP_NOT_FOUND);
        }

        $authUser = auth()->user();
        $userId   = $order->user_id;

        if ($authUser->id !== $userId) {
            return failure('You are not authorized to access this return detail.', Response::HTTP_FORBIDDEN);
        }

        $city     = $order->customer_location;
        $district = $city?->parent;
        $division = $district?->parent;

        return success('Customer returns', [
            'order'             => (int) $merchantOrder->id,
            'tracking_id'       => $merchantOrder->tracking_id,
            'sub_total'         => $merchantOrder->sub_total,
            'total_amount'      => $merchantOrder->total_amount,
            'shipping_amount'   => $merchantOrder->shipping_amount,
            'discount_amount'   => $merchantOrder->discount_amount,
            'charge'            => 0, // $merchantOrder->charge,
            'status_id'         => (int) $merchantOrder->status_id->value,
            'status'            => $merchantOrder->status_label,
            'customer_name'     => $merchantOrder->order->customer_name,
            'customer_number'   => $merchantOrder->order->customer_number,
            'customer_landmark' => $merchantOrder->order->customer_landmark,
            'customer_address'  => $merchantOrder->order->customer_address,
            'city'              => $city?->name,
            'district'          => $district?->name,
            'division'          => $division?->name,
            'created_at'        => $merchantOrder->created_at,
            'shop_id'           => (int) $merchantOrder->merchant->id,
            'shop_name'         => $merchantOrder->merchant->shop_name,
            'shop_slug'         => $merchantOrder->merchant->slug,
            'shop_image'        => $merchantOrder->merchant->shop_logo ?? '',
            'steadfast_status'  => self::steadfastReturnStatus($merchantOrder->return_request_id, $merchantOrder->merchant_id),
            'order_item'        => tap($merchantOrder->orderItems->firstWhere('id', $item_id), function ($item) {
                if (! $item) {
                    return;
                }

                $thumbnail = $item->product->thumbnail;
                if (isset($item->product_variant) && $item->product_variant->image) {
                    $thumbnail = $item->product_variant->image;
                }

                $item->formatted = [
                    'id'                    => (int) $item->id,
                    'price'                 => $item->item_final_price,
                    'quantity'              => $item->quantity,
                    'total_amount'          => ($item->item_final_price * $item->quantity),
                    'product_name'          => $item->product->name,
                    'product_thumbnail'     => $thumbnail ?? '',
                    'product_slug'          => $item->product->slug,
                    'product_sku'           => $item->product?->sku,
                    'status'                => $item->status_label,
                    'return_status_id'      => $item->itemCase?->status,
                    'return_status'         => $item->itemCase?->status_label,
                    'return_status_message' => self::returnStatusMessage($item->itemCase?->status_label),
                    'is_reviewed'           => (bool) $item->review,
                    'product_variant'       => OrderService::getOrderItemVariantText($item->product_variant->variations ?? []),
                ];
            })->formatted ?? null,
        ], Response::HTTP_OK, ['customer_number']);
    }

    public static function steadfastReturnStatus(?int $returnRequestId, ?int $merchantId): ?string
    {
        $sfcConfig = ShopSetting::whereIn('key', ['sfc_base_url', 'sfc_public_key', 'sfc_secret_key'])->pluck('value', 'key')->toArray();

        if (empty($sfcConfig['sfc_base_url']) || empty($sfcConfig['sfc_public_key']) || empty($sfcConfig['sfc_secret_key'])) {
            DB::rollBack();

            return ApiResponse::failure('SFC Configuration problem, please contact admin', Response::HTTP_NOT_FOUND);
        }

        $apiUrl = $sfcConfig['sfc_base_url'].'/get_return_request/'.$returnRequestId;

        $headers = [
            'api-key'    => $sfcConfig['sfc_public_key'],
            'secret-key' => $sfcConfig['sfc_secret_key'],
        ];

        $response = Http::withHeaders($headers)->get($apiUrl);

        if ($response->successful()) {
            $data = json_decode($response->body(), true);

            return $data['data']['status'];
        } else {
            return null;
        }
    }

    public static function recalculateShipping(MerchantOrder $merchantOrder, string $shippingType)
    {
        $editableStatuses = [
            OrderStatus::PENDING->value,
            OrderStatus::APPROVED->value,
            OrderStatus::PROCESSING->value,
            OrderStatus::READY_TO_SHIP->value,
        ];

        $orderItems = $merchantOrder->orderItems()
            ->whereIn('status_id', $editableStatuses)
            ->with(['product.shopProduct', 'product_variant.shopVariation'])
            ->get();

        if (blank($orderItems)) {
            return 0;
        }

        $deliveryType = $merchantOrder->delivery_type->value;

        $key = match ($shippingType) {
            'ISD'   => $deliveryType == 2 ? 'ed_delivery_fee' : 'id_delivery_fee',
            default => 'od_delivery_fee'
        };

        return $merchantOrder->merchant->getDeliveryCharges()[$key];
    }

    private static function getOrderStatusMessage($statusLabel): string
    {
        return match (strtolower($statusLabel)) {
            'pending'        => 'Your order is waiting for confirmation ',
            'processing'     => 'Your order is being processed',
            'approved'       => 'Your order is being approved ',
            'cancelled'      => 'Your order was cancelled ',
            'ready to ship'  => 'Your order is ready for shipment ',
            'delivered'      => 'Your order has been delivered .',
            'returned'       => 'Your returned order is being handled at the return center.',
            default          => 'Unknown order status',
        };
    }

    public static function returnStatusMessage($statusLabel): string
    {
        return match (strtolower($statusLabel)) {
            'pending'    => 'We’ve received your return request and it’s currently under review.',
            'accepted'   => 'Your return request has been accepted.',
            'approved'   => 'Your return request has been approved.',
            'processing' => 'Your return is being processed. We’ll update you once it’s completed.',
            'refunded'   => 'The refund has been successfully issued to your original payment method.',
            'rejected'   => 'Unfortunately, your return request was not approved.',
            'completed'  => 'Your return has been completed successfully.',
            'declined'   => 'Your return request has been declined.',
            'cancelled'  => 'Your return request has been cancelled.',

            default      => 'We’re unable to determine the return status at this time.',
        };
    }
}
