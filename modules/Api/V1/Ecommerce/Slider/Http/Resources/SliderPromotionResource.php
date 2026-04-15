<?php

namespace Modules\Api\V1\Ecommerce\Slider\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Api\V1\Ecommerce\Product\Http\Resources\ProductsResource;

class SliderPromotionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'slider_id' => $this->id,
            'slider_link' => $this->link,
            'slider_type' => $this->label,
            'label_name' => $this->label_name,
            'label_slug' => $this->label_slug,
            'mobile_image' => $this->small_image,
            'web_image' => $this->full_image,
            'mobile_label_banner' => $this->mobile_label_banner,
            'desktop_label_banner' => $this->desktop_label_banner,
            'promotion_products' => ProductsResource::collection($this->slider_products),
        ];
    }
}
