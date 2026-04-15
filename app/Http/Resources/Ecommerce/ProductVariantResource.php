<?php

namespace App\Http\Resources\Ecommerce;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariantResource extends JsonResource
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
            'attribute_option_id' => intval($this->attribute_option_id),
            'attribute_id'        => intval($this->attribute_id),
            'attribute_name'      => $this->attribute->name,
            'attribute_option'    => $this->attributeOption->attribute_value,
        ];
    }
}
