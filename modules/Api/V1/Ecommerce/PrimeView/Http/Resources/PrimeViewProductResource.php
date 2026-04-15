<?php

namespace Modules\Api\V1\Ecommerce\PrimeView\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Api\V1\Ecommerce\Product\Http\Resources\ProductsResource;

class PrimeViewProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'slug'       => $this->slug,
            'background' => empty($this->background) ? null : $this->background,
            'start_date' => $this->start_date   ?? null,
            'end_date'   => $this->end_date     ?? null,
            'menu_icon'  => $this->menu_icon     ?? null,
            'products'   => ProductsResource::collection($this->products),
        ];
    }
}
