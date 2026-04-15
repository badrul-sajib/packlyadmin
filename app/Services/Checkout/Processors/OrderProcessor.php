<?php

namespace App\Services\Checkout\Processors;

use App\DTOs\CheckoutData;
use App\Enums\CouponApplyOn;
use App\Models\Coupon\Coupon;
use App\Models\Coupon\CouponUsage;
use App\Models\Order\CustomerAddress;
use App\Models\Order\Order;
use Exception;
use Illuminate\Validation\ValidationException;

class OrderProcessor
{
    /**
     * @throws Exception
     */
    public function createOrder(CheckoutData $data, CustomerAddress $address, array $orderItems, array $shippingDetails, $coupon = null): Order
    {
        $totalItems = $this->totalItems($orderItems);
        $totalPrice = $this->calculateTotalPrice($orderItems);
        $totalProductDiscount = $this->calculateProductDiscount($orderItems);
        $subtotalPrice = $this->calculateSubTotalPrice($orderItems);
        $discount_amount = 0;
        $total_shipping_fee = $shippingDetails['total_shipping_fee'];
        $couponData = Coupon::where('code', $data->coupon_code)->first();

        if ($couponData && $couponData->apply_on == CouponApplyOn::SHIPPING_CHARGE->value) {
            $total_shipping_fee = $coupon['discount_amount'] > $shippingDetails['total_shipping_fee'] ? 0 : $shippingDetails['total_shipping_fee'] - $coupon['discount_amount'];
        } else {
            $discount_amount = $coupon?->sum('shop_discounts') ?? 0;
        }

        $grandTotal = $subtotalPrice + $total_shipping_fee - $discount_amount;

        if ($grandTotal != $data->final_payable_price) {
            throw ValidationException::withMessages(['final_payable_price' => 'Invalid final payable price, expected: ' . $grandTotal]);
        }

        $order = Order::create([
            'invoice_id' => getInvoiceNo('orders', 'invoice_id', 'INV'),
            'user_id' => auth()->id(),
            'customer_location_id' => $address->location_id,
            'customer_name' =>  $address->name ?? 'N/A',
            'customer_address' => $address->address,
            'customer_landmark' => $address->landmark,
            'customer_number' => $address->contact_number,
            'total_items' => $totalItems,
            'total_amount' => $totalPrice,
            'item_discount' => $totalProductDiscount,
            'sub_total' => $subtotalPrice,
            'total_discount' => $discount_amount,
            'total_shipping_fee' => $total_shipping_fee,
            'grand_total' => $grandTotal,
            'order_from' => $data->order_from,
            'shipping_type' => $shippingDetails['shipping_type'],

            // UTM Tracking
            'utm_source'    => $data->utmSource,
            'utm_medium'    => $data->utmMedium,
            'utm_campaign'  => $data->utmCampaign,
            'utm_term'      => $data->utmTerm,
            'utm_content'   => $data->utmContent,
            'utm_id'        => $data->utmId,
        ]);

        if ($couponData && $couponData->apply_on == CouponApplyOn::SHIPPING_CHARGE->value) {

            CouponUsage::create([
                'user_id' => auth()->user()->id,
                'coupon_id' => $couponData->id,
                'order_id' => $order->id,
                'discount_amount' => $couponData->discount_value,
                'discount_type' => $couponData->discount_type,
                'min_purchase' => $couponData->min_purchase,
                'max_discount' => $couponData->max_discount_value,
                'coupon_type' => $couponData->apply_on,
                'bear_by_packly' => 1,
            ]);
        }

        return $order;
    }

    private function calculateSubTotalPrice(array $orderItems): float
    {
        return collect($orderItems)->flatten(1)->sum(fn($item) => $item['price'] * $item['quantity']);
    }

    private function calculateTotalPrice(array $orderItems): float
    {
        return collect($orderItems)->flatten(1)->sum(fn($item) => $item['regular_price'] * $item['quantity']);
    }

    public function calculateProductDiscount(array $orderItems)
    {
        return collect($orderItems)->flatten(1)->sum(fn($item) => ($item['regular_price'] - $item['price']) * $item['quantity']);
    }

    public function totalItems(array $orderItems)
    {
        return collect($orderItems)->flatten(1)->sum(fn($item) => $item['quantity']);
    }
}
