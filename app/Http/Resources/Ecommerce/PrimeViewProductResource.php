<?php

namespace App\Http\Resources\Ecommerce;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
