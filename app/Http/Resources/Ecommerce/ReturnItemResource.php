<?php

namespace App\Http\Resources\Ecommerce;

use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReturnItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $thumbnail = $this->product?->thumbnail;
        if (isset($this->product_variant) and $this->product_variant->image) {
            $thumbnail = $this->product_variant->image;
        }

        return [
            'id'                   => intval($this->id),
            'price'                => $this->item_final_price,
            'quantity'             => $this->quantity,
            'status'               => $this->status_label,
            'product_name'         => $this->product?->name,
            'product_slug'         => $this->product?->slug,
            'product_thumbnail'    => $thumbnail,
            'product_rating_avg'   => $this->product?->rating_avg,
            'product_rating_count' => $this->product?->rating_count,
            'product_variant'      => OrderService::getOrderItemVariantText($this->product_variant?->variations ?? []),
            'product_sku'          => $this->product_variation_id ? $this->product_variant?->sku : $this->product?->sku,
            'shop_id'              => $this->merchant?->merchant?->id,
            'shop_name'            => $this->merchant?->merchant?->shop_name,
            'shop_slug'            => $this->merchant?->merchant?->slug,
            'shop_image'           => $this->merchant?->merchant?->shop_logo ?? null,
        ];
    }
}
