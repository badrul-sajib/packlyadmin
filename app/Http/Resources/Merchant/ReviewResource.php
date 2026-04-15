<?php

namespace App\Http\Resources\Merchant;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'review'          => $this->review,
            'review_reply'    => $this->review_reply ?? null,
            'rating'          => $this->rating,
            'seller_rating'   => $this->seller_rating,
            'shipping_rating' => $this->shipping_rating,
            'is_approved'     => $this->is_approved,
            'product'         => (object) [
                'id'        => $this->product->id,
                'name'      => $this->product->name,
                'slug'      => $this->product->slug,
                'thumbnail' => $this->product->thumbnail,
                'variant'   => (object) [
                    'id'  => $this->orderItem?->product_variant?->id,
                    'sku' => $this->orderItem?->product_variant?->sku,
                ],
            ],
            'user' => (object) [
                'id'    => $this->user->id,
                'name'  => $this->user->name,
                'image' => $this->user->image,
            ],
            'order' => (object) [
                'id'             => $this->orderItem?->merchantOrder?->id,
                'invoice_number' => $this->orderItem?->merchantOrder?->tracking_id,
            ],
            'created_at' => $this->created_at->diffForHumans(),
        ];
    }
}
