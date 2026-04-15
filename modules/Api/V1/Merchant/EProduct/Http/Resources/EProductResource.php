<?php

namespace Modules\Api\V1\Merchant\EProduct\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $defaultVariation = optional($this->productDetail)->selectedVariation;
        return [
            'id'                => $this->shopProduct->id,
            'status'            => $this->shopProduct->status,
            'created_at'        => $this->shopProduct->created_at,
            'status_label'      => $this->shopProduct->status_label,
            'e_price'           =>  $this->shopProduct->e_price,
            'e_discount_price'  =>  $this->shopProduct->e_discount_price,
            'product'           => [
                'id'                => $this->id,
                'name'              => $this->name,
                'slug'              => $this->slug,
                'sku'               => $this->product_type_id == 1 ? $this->sku : $defaultVariation?->sku,
                'total_stock_qty'   => $this->product_type_id == 1 ? $this->total_stock_qty : $defaultVariation?->total_stock_qty ?? 0,
                'thumbnail'         => $this->thumbnail,
                'product_type_id'   => $this->product_type_id,
                'rating_avg'        => $this->rating_avg,
                'rating_count'      => $this->rating_count,
                'category'          => [
                    'id' => $this->category->id,
                    'name' => $this->category->name,
                ],
            ],
        ];
    }
}
