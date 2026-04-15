<?php

namespace App\Http\Resources\Ecommerce;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetails extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'tracking_id' => $this->tracking_id,
            'order_id'    => $this->order_id,
            'merchant'    => [
                'id'        => $this->merchant->id,
                'name'      => $this->merchant->name,
                'shop_name' => $this->merchant->shop_name,
            ],
            'status' => [
                'id'    => $this->status_id,
                'label' => $this->status_label,
                'color' => $this->status_bg_color,
            ],
            'summary' => [
                'total_items'   => $this->order_items_count,
                'subtotal'      => $this->sub_total,
                'item_discount' => $this->item_discount,
                'shipping_fee'  => $this->shipping_amount,
                'grand_total'   => $this->grand_total,
                'delivery_type' => $this->delivery_type == 1 ? 'Instant Delivery' : 'Standard Delivery',
            ],
            'order_date' => $this->created_at->format('M d, Y'),
            'order_time' => $this->created_at->format('h:i A'),
        ];
    }
}
