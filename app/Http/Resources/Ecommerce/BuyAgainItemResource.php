<?php

namespace App\Http\Resources\Ecommerce;

use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BuyAgainItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $thumbnail = $this->product->thumbnail;

        return [
            'id'                 => $this->id,
            'product_thumbnail'  => $thumbnail,
            'product_name'       => $this->product->name,
            'product_slug'       => $this->product->slug,
            'regular_price'      => $this->product->productDetail->e_price,
            'discount_price'     => discount_price($this->product->id,$this->product->productDetail->e_price, $this->product->productDetail->e_discount_price),
            'available_stock'    => intval($this->product->total_stock_qty),
            'is_variant'         => $this->product->is_variant,
            'product_variant_id' => $this->product_variant->id,
            'product_variant'    => OrderService::getOrderItemVariantText($this->product_variant->variations ?? []),
        ];
    }
}
