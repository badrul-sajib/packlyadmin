<?php

namespace Modules\Api\V1\Ecommerce\Payment\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentMethodResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'name'           => $this->name,
            'payment_method' => $this->payment_method,
            'image'          => $this->image,
        ];
    }
}
