<?php

namespace App\Http\Resources\Ecommerce;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewDetailsResource extends JsonResource
{
    public function toArray(Request $request): object
    {
        return (object) [
            'id'                => $this->id,
            'review'            => $this->review,
            'review_reply'      => $this->review_reply,
            'rating'            => $this->rating,
            'is_approved'       => $this->is_approved,
            'is_public'         => $this->is_public,
            'seller_rating'     => $this->seller_rating,
            'shipping_rating'   => $this->shipping_rating,
            'images'            => $this->images,
            'item'              => new OrderItemResource($this->orderItem),
        ];
    }
}
