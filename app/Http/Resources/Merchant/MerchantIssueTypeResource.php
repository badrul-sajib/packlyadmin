<?php

namespace App\Http\Resources\Merchant;

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
