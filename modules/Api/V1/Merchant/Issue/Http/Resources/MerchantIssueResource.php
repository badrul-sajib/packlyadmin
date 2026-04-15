<?php

namespace Modules\Api\V1\Merchant\Issue\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MerchantIssueResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'        => (int) $this->id,
            'type'      => [
                'id'   => (int) $this->type->id,
                'name' => (string) $this->type->name,
            ],
            'message'     => (string) $this->message,
            'status'      => (int) $this->status,
            'attachments' => $this->getUrl('attachments'),
            'created_at'  => $this->created_at?->format('Y/m/d H:i'),
        ];
    }
}
