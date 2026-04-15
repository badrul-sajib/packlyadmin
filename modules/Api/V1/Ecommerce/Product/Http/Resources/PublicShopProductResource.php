<?php

namespace Modules\Api\V1\Ecommerce\Product\Http\Resources;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PublicShopProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $thumbnail = $this->thumbnail_path ?? null;
        if ($thumbnail) {
            $thumbnail = Storage::disk($this->getDiskName())->url($thumbnail);
        }

        return [
            'id'            => (int) ($this->product_id ?? $this->id),
            'name'          => $this->name,
            'merchant_id'     => $this->merchant_id ? (int) $this->merchant_id : null,
            'merchant_name' => $this->merchant_name ?? $this->merchant?->name ?? null,
            'shop_name'     => $this->shop_name ?? $this->merchant?->shop_name ?? null,
            'category_name' => $this->category_name ?? null,
            'sub_category_name'       => $this->sub_category_name ?? null,
            'sub_category_child_name' => $this->child_category_name ?? null,
            'brand_name'    => $this->brand_name ?? null,
            'description'   => $this->description ?? null,
            'specification'   => $this->specification ?? null,
            'image_url'     => $thumbnail ?? ($this->thumbnail ?? null),
            'price'         => $this->e_discount_price ?? $this->e_price ?? null,
            'rating'        => (float) ($this->total_rating ?? $this->rating_avg ?? 0),
            'review_count'  => (int) ($this->total_review ?? $this->rating_count ?? 0),
        ];
    }
}
