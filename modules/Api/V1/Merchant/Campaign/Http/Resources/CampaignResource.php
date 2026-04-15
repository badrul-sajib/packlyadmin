<?php

namespace Modules\Api\V1\Merchant\Campaign\Http\Resources;

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
        $now = now();
        
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'slug'       => $this->slug,
            'starts_at'  => $this->starts_at,
            'ends_at'    => $this->ends_at,
            'status'     => $this->status,
            'status_label' => $this->status->label(),
            'active'     => $now->between($this->starts_at, $this->ends_at),
            'vendor_request_start' => $this->vendor_request_start,
            'vendor_request_end' => $this->vendor_request_end,
            'visibility_rules' => $this->visibility_rules,
            'image'      => $this->image,
            'prime_views' => PrimeViewResource::collection($this->primeViews),
        ];
    }
}
