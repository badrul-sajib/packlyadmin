<?php

namespace App\Http\Resources\Merchant;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'merchant_id' => $this->merchant_id,
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
            'customer_type_id' => $this->customer_type_id,
            'customer_type_label' => $this->customer_type_id?->getValues(),
            'balance' => $this->balance,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'image' => $this->image,
            'media' => [],
            'sell_product_summary' => [
                'sell_product_count' => (int) ($this->sell_product_count ?? 0),
                'total_sales_amount' => (float) ($this->total_sales_amount ?? 0),
                'total_paid_amount' => (float) ($this->total_paid_amount ?? 0),
                'total_due_amount' => (float) ($this->total_due_amount ?? 0),
            ],
        ];
    }
}
