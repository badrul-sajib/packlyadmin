<?php

namespace Modules\Api\V1\Merchant\Campaign\Http\Resources;
use Modules\Api\V1\Ecommerce\Product\Http\Resources\ProductsResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PrimeViewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "slug" => $this->slug,
            "image" => $this->image,
            "background" => $this->background,
            "menu_icon" => $this->menu_icon,
            "discount_amount" => $this->pivot->discount_amount,
            "discount_type" => $this->pivot->discount_type,
            "rules" => $this->pivot->rules,
            "products" => ProductsResource::collection($this->products),
        ];
    }
}
