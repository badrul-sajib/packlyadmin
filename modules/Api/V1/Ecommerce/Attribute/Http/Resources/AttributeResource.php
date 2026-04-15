<?php

namespace Modules\Api\V1\Ecommerce\Attribute\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AttributeResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'attribute_option' => $this->options->map(function ($item) {
                return [
                    'id' => $item->id,
                    'value' => $item->attribute_value,
                ];
            })->values(),
        ];
    }
}
