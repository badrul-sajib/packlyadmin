<?php

namespace App\Http\Resources\Ecommerce;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name'         => $this->name,
            'coupon_code'  => $this->code,
            'description'  => $this->description,
            'type'         => $this->discount_type,
            'amount'       => $this->discount_value,
            'min_purchase' => $this->min_purchase,
            'start_date'   => $this->start_date,
            'end_date'     => $this->end_date,
        ];
    }
}
