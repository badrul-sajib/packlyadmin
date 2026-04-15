<?php

namespace App\Http\Resources\Ecommerce;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AllOrderListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $hasPendingPayment = $this->merchantOrders()
            ->whereHas('payment', function ($query) {
                $query->where('payment_method', PaymentMethod::SSLCOMMERZ->value)
                    ->where('payment_status', PaymentStatus::PENDING->value);
            })
            ->exists();

        return [
            'order_id'            => $this->id,
            'is_to_pay'           => $hasPendingPayment,
            'sub_total'           => $this->sub_total,
            'shipping_fee'        => $this->total_shipping_fee,
            'discount_amount'     => $this->total_discount,
            'total_amount'        => $this->grand_total,
            'payment_method'      => $this->merchantOrders()->first()->payment?->payment_method,
            'payment_status'      => $this->merchantOrders()->first()->payment?->status_label,
            'customer_name'       => $this->customer_name,
            'customer_number'     => $this->customer_number,
            'customer_landmark'   => $this->customer_landmark,
            'customer_address'    => $this->customer_address,
            'date'                => $this->created_at,
            'total_items_count'   => $this->total_items,
            'merchant_orders'     => OrderShopResource::collection($this->merchantOrders),
        ];
    }
}
