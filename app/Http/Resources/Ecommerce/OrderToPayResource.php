<?php

namespace App\Http\Resources\Ecommerce;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderToPayResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'order_id'            => $this->id,
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

    private function getBuyAgainItems()
    {
        $stockItems = $this->orderItems
            ->filter(function ($item) {
                return $item->product && $item->product->total_stock_qty > 0;
            })->count();
        if (count($this->orderItems) == $stockItems) {
            return true;
        }

        return false;
    }
}
