<?php

namespace App\Http\Resources\Ecommerce;

use App\Services\CustomerOrderService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderReturnResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $total = ($this->quantity ?? 0) * ($this->item_final_price ?? 0);

        return [
            'id'                    => intval($this->id),
            'tracking_id'           => $this->merchant?->tracking_id,
            'item_total_discount'   => $this->coupon_discount,
            'total_amount'          => $total, //
            'shipping_amount'       => $this->merchant?->shipping_amount,
            'charge'                => 0, // $this->merchant->charge,
            'status'                => $this->itemCase?->status_label,
            'return_status_id'      => $this->itemCase?->status,
            'return_status'         => $this->itemCase?->status_label,
            'return_status_message' => CustomerOrderService::returnStatusMessage($this->itemCase?->status_label),
            'shop_id'               => intval($this->merchant?->merchant?->id),
            'shop_name'             => $this->merchant?->merchant?->shop_name,
            'shop_slug'             => $this->merchant?->merchant?->slug,
            'shop_image'            => $this->merchant?->merchant?->shop_logo ?? null,
            'order_created_at'      => $this->merchant?->created_at?->format('Y-m-d h:i A'),
            'status_updated_at'     => $this->itemCase?->updated_at?->format('Y-m-d h:i A'),
            'items'                 => [new ReturnItemResource($this)],
        ];
    }
}
