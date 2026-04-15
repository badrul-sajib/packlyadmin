<?php

namespace Modules\Api\V1\Merchant\Issue\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MerchantIssueTypeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'   => (int) $this->id,
            'name' => (string) $this->name,
        ];
    }
}
