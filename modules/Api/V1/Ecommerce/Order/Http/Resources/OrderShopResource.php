<?php

namespace Modules\Api\V1\Ecommerce\Order\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderShopResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                  => intval($this->id),
            'parent_order_id'     => $this->order_id,
            'parent_total_amount' => $this->order->grand_total,
            'payment_method'      => $this->payment?->payment_method,
            'payment_status'      => $this->payment?->status_label,
            'tracking_id'         => $this->tracking_id,
            'total_amount'        => $this->grand_total - $this->shipping_amount,
            'shipping_amount'     => $this->shipping_amount,
            'grand_total'         => $this->grand_total,
            'discount_amount'     => $this->discount_amount,
            'charge'              => 0, // $this->charge,
            'status'              => $this->status_label,
            'shop_id'             => intval($this->merchant->id),
            'shop_name'           => $this->merchant->shop_name,
            'shop_slug'           => $this->merchant->slug,
            'shop_image'          => $this->merchant->shop_logo ?? null,
            'total_items_count'   => $this->total_items,
            'items'               => OrderItemResource::collection($this->orderItems),
            'buy_again'           => $this->getBuyAgainItems(),
            'date'                => Carbon::parse($this->order->getRawOriginal('created_at'))->format('d/m/Y h:i A'),
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
