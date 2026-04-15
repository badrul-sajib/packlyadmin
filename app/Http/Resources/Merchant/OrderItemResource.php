<?php

namespace App\Http\Resources\Merchant;

use App\Enums\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $product = $this->product;

        return [
            'id'                   => $product->id,
            'name'                 => $product->name,
            'image'                => $product->thumbnail,
            'price'                => $this->price,
            'quantity'             => $this->quantity,
            'status_id'            => $this->status_id,
            'is_variation'         => $this->product_variation_id ? true : false,
            'product_variation_id' => $this->product_variation_id,
            'sku'                  => $this->product_variation_id ? $this->product_variant->sku : $this->product->sku,
            'commission'           => $this->commission ?? 0,
            'commission_value'     => $this->commission_value ?? 0,
            'commission_type'      => $this->commission_type ?? 0,
            'variant'              => $this->product_variant,
            'attributes'           => $this->product_variation_id ? $this->product_variant->variationAttributes->map(function ($attribute) {
                return [
                    'name'  => $attribute->attribute->name,
                    'value' => $attribute->attributeOption->value,
                ];
            }) : null,
            'cancel_reason' => $this->when(
                $this->status_id == OrderStatus::CANCELLED->value,
                fn() => $this->itemCase?->reason?->name
            ),
        ];
    }
}
