<?php

namespace App\Services\Checkout\Processors;

use App\Enums\CouponApplyOn;
use App\Enums\OrderStatus;
use App\Enums\OrderStatusTimelineTypes;
use App\Models\Coupon\Coupon;
use App\Models\Coupon\CouponUsage;
use App\Models\Merchant\MerchantOrder;
use App\Models\Merchant\MerchantOrderTimeline;
use App\Models\Order\Order;
use App\Models\Order\OrderItem;
use App\Models\Product\Product;
use App\Traits\ProductCommission;
use Exception;

class MerchantOrderProcessor
{
    use ProductCommission;

    /**
     * @throws Exception
     */
    public function createMerchantOrders(Order $order, array $orderItems, array $shippingDetails, $coupon = null): array
    {
        $merchantOrders = [];
        foreach ($orderItems as $merchantId => $items) {
            $merchantTotalItems = $this->calculateMerchantTotalItems($items);
            $merchantTotal = $this->calculateMerchantTotal($items);
            $merchantSubTotal = $this->calculateMerchantSubTotal($items);
            $merchantProductDiscount = $this->calculateMerchantProductDiscount($items);
            $shopCoupon = null;

            if (isset($coupon['type']) && $coupon['type'] == CouponApplyOn::SHIPPING_CHARGE->value) {
                $shopCoupon = (object) [
                    'coupon_id' => $coupon['coupon_id'] ?? 0,
                    'shop_discounts' => 0,
                    'products' => [],
                ];
            } else {
                $shopCoupon = $coupon?->filter(fn ($coupon) => $coupon->shop_id == $merchantId && $coupon->is_eligible_shop)->first() ?? null;
            }

            $merchantOrder = $this->createMerchantOrder($order, $merchantId, $merchantTotal, $merchantProductDiscount, $merchantSubTotal, $shippingDetails, $shopCoupon, $merchantTotalItems);
            $merchantOrders[] = $merchantOrder;

            $this->createOrderItems($merchantOrder, $items, $shopCoupon);

            try {
                $notification = "Hi {$merchantOrder->merchant->name}, you have a new order. from {$order->customer_name}";
                $merchantOrder->merchant->sendNotification('New Order', $notification, '/orders');
            } catch (\Throwable $th) {
                // Log::error($th->getMessage());
            }
        }

        return $merchantOrders;
    }

    private function calculateMerchantTotal(array $items): float
    {
        return collect($items)->sum(fn ($item) => $item['regular_price'] * $item['quantity']);
    }

    private function calculateMerchantProductDiscount(array $items): float
    {
        return collect($items)->sum(fn ($item) => ($item['regular_price'] - $item['price']) * $item['quantity']);
    }

    private function calculateMerchantSubTotal(array $items): float
    {
        return collect($items)->sum(fn ($item) => $item['price'] * $item['quantity']);
    }

    private function calculateMerchantTotalItems(array $items): float
    {
        return collect($items)->sum(fn ($item) => $item['quantity']);
    }

    /**
     * @throws Exception
     */
    private function createMerchantOrder(Order $order, int $merchantId, float $merchantTotal, float $merchantProductDiscount, float $merchantSubTotal, array $shippingDetails, $shopCoupon, $merchantTotalItems): MerchantOrder
    {
        $validMerchantFreeShip = $shippingDetails['merchantFreeShipping'][$merchantId] ?? false;
        $coupon_discount = $shopCoupon->shop_discounts ?? 0;
        $totalShipping = $shippingDetails['merchant_shipping_details'][$merchantId]['delivery_charge'];
        $totalWeight = $shippingDetails['merchant_shipping_details'][$merchantId]['total_weight'];
        $shippingAmount = $validMerchantFreeShip ? 0 : $totalShipping;
        $grandTotal = $merchantSubTotal + $shippingAmount - $coupon_discount;

        $merchantOrder = MerchantOrder::create([
            'order_id' => $order->id,
            'merchant_id' => $merchantId,
            'invoice_id' => getInvoiceNo('merchant_orders', 'invoice_id', 'INV', 10),
            'tracking_id' => getInvoiceNo('merchant_orders', 'tracking_id', 'TRK', 10),
            'status_id' => OrderStatus::PENDING->value,
            'total_items' => $merchantTotalItems,
            'total_amount' => $merchantTotal,
            'item_discount' => $merchantProductDiscount,
            'sub_total' => $merchantSubTotal,
            'discount_amount' => $coupon_discount,
            'shipping_amount' => $validMerchantFreeShip ? 0 : $totalShipping,
            'grand_total' => $grandTotal,
            'total_weight' => $totalWeight,
            'delivery_type' => $shippingDetails['merchant_shipping_details'][$merchantId]['delivery_type'] ?? 1,
            'bear_by_packly' => $shopCoupon->bear_by_packly ?? null,
        ]);

        $this->createOrderTimeLine($merchantOrder->id);

        $this->createCouponUsages($shopCoupon, $merchantOrder->id);

        return $merchantOrder;
    }

    public function createOrderTimeLine($merchantOrderId): void
    {
        foreach (OrderStatus::cases() as $status) {
            MerchantOrderTimeline::create([
                'merchant_order_id' => $merchantOrderId,
                'status_id' => $status->value,
                'type' => OrderStatusTimelineTypes::ORDER->value,
                'message' => OrderStatus::getStatusMessage()[$status->value],
                'date' => $status->value == OrderStatus::PENDING->value ? now() : null,
            ]);
        }
    }

    private function createOrderItems(MerchantOrder $merchantOrder, array $items, $shopCoupon = null): void
    {
        foreach ($items as $item) {

            $product = Product::find($item['product_id']);
            $commissionData = $this->getProductCommission($product);
            $commission = $commissionData->commission;
            $commissionType = $commissionData->commission_type;

            $couponProducts = collect($shopCoupon->products ?? []) ?? [];

            if ($shopCoupon && $shopCoupon->products) {
                if (! empty($item['product_variation_id'])) {
                    $couponProducts = $couponProducts->filter(fn ($couponProduct) => $couponProduct->product_id == $item['product_id'] && $couponProduct->product_variation_id == $item['product_variation_id'])->first();
                } else {
                    $couponProducts = $couponProducts->filter(fn ($couponProduct) => $couponProduct->product_id == $item['product_id'])->first();
                }
            } else {
                $couponProducts = null;
            }

            $total =

            OrderItem::create([
                'merchant_order_id' => $merchantOrder->id,
                'product_id' => $item['product_id'],
                'product_variation_id' => $item['product_variation_id'],
                'quantity' => $item['quantity'],
                'regular_price' => $item['regular_price'],
                'price' => $item['price'],
                'discount_amount' => $item['regular_price'] - $item['price'],
                'status_id' => OrderStatus::PENDING->value,
                'commission' => $this->getCommission($commissionType, $commission, $item['price']) * $item['quantity'],
                'commission_value' => $commission,
                'commission_type' => $commissionType == 'percent' ? 1 : 2,
                'coupon_discount' => $couponProducts?->discount_amount ?? 0,
                'coupon_discount_percentage' => $couponProducts?->discount_percentage ?? 0,
                'item_final_price' => $item['price'] - ($couponProducts?->discount_amount / $item['quantity'] ?? 0),
            ]);
        }
    }

    private function createCouponUsages($shopCoupon, $orderId): void
    {

        if (! $shopCoupon) {
            return;
        }
        if (count($shopCoupon->products) == 0) {
            return;
        }

        $coupon = Coupon::find($shopCoupon->coupon_id);

        CouponUsage::create([
            'user_id' => auth()->user()->id,
            'coupon_id' => $coupon->id,
            'merchant_order_id' => $orderId,
            'discount_amount' => $coupon->discount_value,
            'discount_type' => $coupon->discount_type,
            'min_purchase' => $coupon->min_purchase,
            'max_discount' => $coupon->max_discount_value,
            'coupon_type' => CouponApplyOn::PRODUCT_PRICE->value,
            'bear_by_packly' => $coupon->bear_by_packly,
        ]);
    }
}
