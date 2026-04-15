<?php

namespace App\Http\Resources\Ecommerce;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GiveawayTicketResource extends JsonResource
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
            'ticket_number' => $this->ticket_number,
            'is_winner' => $this->is_winner,
            'is_valid' => count($this->order?->merchantOrders) > 0 ? true : false,
            'end_date' => $this->giveaway?->end_at->format('Y-m-d H:i:s'),
        ];
    }
}
