<?php

namespace App\Http\Resources\Ecommerce;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class ProductVariationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $charges = $this->merchant->getDeliveryCharges();

        return [
            'id'             => intval($this->id),
            'sku'            => $this->sku,
            'regular_price'  => (string) $this->shopVariation->e_price,
            'discount_price' => $this->shopVariation->e_discount_price,

            // delivery
            'id_delivery_fee'    => Arr::get($charges, 'id_delivery_fee', '0'),
            'od_delivery_fee'    => Arr::get($charges, 'od_delivery_fee', '0'),
            'ed_delivery_fee'    => Arr::get($charges, 'ed_delivery_fee', '0'),

            'quantity' => intval($this->total_stock_qty),
            'image'    => $this->image,
            'variant'  => ProductVariantResource::collection($this->variationAttributes()->get()),
        ];
    }
}
