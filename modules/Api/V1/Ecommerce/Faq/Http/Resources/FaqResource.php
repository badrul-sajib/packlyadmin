<?php

namespace Modules\Api\V1\Ecommerce\Faq\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FaqResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'question'   => $this->question,
            'answer'     => $this->answer,
        ];
    }
}
