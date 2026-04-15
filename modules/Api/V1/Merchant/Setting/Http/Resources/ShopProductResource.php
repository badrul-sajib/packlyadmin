<?php

namespace Modules\Api\V1\Merchant\Setting\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShopProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'id'              => intval($this->id),
            'name'            => $this->name,
            'slug'            => $this->slug,
            'regular_price'   => $this->shopProduct->e_price,
            'discount_price'  => $this->shopProduct->e_discount_price,
            'is_variant'      => $this->is_variant,
            'thumbnail'       => $this->thumbnail,
            'rating_avg'      => $this->rating_avg,
            'rating_count'    => $this->rating_count,
            'available_stock' => intval($this->total_stock_qty),
        ];
    }
}
