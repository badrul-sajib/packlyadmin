<?php

namespace Modules\Api\V1\Ecommerce\Campaign\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CampaignResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'start_title' => $this->start_title,
            'end_title' => $this->end_title,
            'start_subtitle' => $this->start_subtitle,
            'end_subtitle' => $this->end_subtitle,
            'slug' => $this->slug,
            'starts_at' => $this->starts_at,
            'ends_at' => $this->ends_at,
            'vendor_request_start' => $this->vendor_request_start,
            'vendor_request_end' => $this->vendor_request_end,
            'visibility_rules' => $this->visibility_rules,
            'image' => $this->image,
            'logo' => $this->logo,
            'prime_views' => PrimeViewResource::collection($this->primeViews),
        ];
    }
}
