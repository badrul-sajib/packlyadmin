<?php

namespace Modules\Api\V1\Merchant\EProduct\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EProductDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                     => $this->id,
            'status'                 => $this->status,
            'created_at'             => $this->created_at,
            'status_label'           => $this->status_label,
            'e_price'                => $this->e_price,
            'e_discount_price'       => $this->e_discount_price,
            'packly_commission'      => $this->packly_commission,
            'packly_commission_type' => $this->packly_commission_type,
            'product'           => [
                'id'                => $this->product->id,
                'name'              => $this->product->name,
                'slug'              => $this->product->slug,
                'sku'               => $this->product->sku,
                'thumbnail'         => $this->product->thumbnail,
                'product_type_id'   => $this->product->product_type_id,
                'total_stock_qty'   => $this->product->total_stock_qty,
                'category'          => [
                    'id' => $this->product->category->id,
                    'name' => $this->product->category->name,
                ],
                'variation_attributes' => collect( $this->product->variationAttributes )->map(function ($variationAttribute) {


                    return [
                        'id' => $variationAttribute->id,
                        'product_variation_id'  => $variationAttribute->product_variation_id,
                        'attribute_id'          => $variationAttribute->attribute_id,
                        'attribute_option_id'   => $variationAttribute->attribute_option_id,
                        'attribute' => [
                            'name' => $variationAttribute->attribute->name,
                            'options' => collect( $variationAttribute->attribute->options )->map(function ($option) {
                                return [
                                    'id' => $option->id,
                                    'attribute_value' => $option->attribute_value,
                                ];
                            }),
                        ],
                    ];
                }),
            ],
            'shop_product_variations' => $this->shopProductVariations,
        ];
    }
}
