<?php

namespace App\Http\Resources\Ecommerce;

use App\Services\OrderService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MyReviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $thumbnail = $this->orderItem?->product?->thumbnail;
        if (isset($this->orderItem?->product_variant) and $this->orderItem?->product_variant?->image) {
            $thumbnail = $this->orderItem?->product_variant?->image;
        }

        return [
            'id'                => intval($this->id),
            'product_id'        => $this->orderItem?->product_id,
            'reviewer_name'     => $this->user?->name ?? null,
            'reviewer_image'    => filled($this->user?->avatar) ? $this->user?->avatar : null,
            'rating'            => $this->rating,
            'shipping_rating'   => $this->shipping_rating,
            'seller_rating'     => $this->seller_rating,
            'review'            => $this->review,
            'seller_reply'      => $this->review_reply ?? null,
            'feedback_images'   => $this->images,
            'product_name'      => $this->orderItem?->product->name,
            'product_slug'      => $this->orderItem?->product->slug,
            'price'             => $this->orderItem?->price,
            'quantity'          => $this->orderItem?->quantity,
            'shop_name'         => $this->orderItem?->product?->merchant?->shop_name,
            'shop_slug'         => $this->orderItem?->product?->merchant?->slug,
            'shop_image'        => $this->orderItem?->product?->merchant?->shop_logo ?? null,
            'product_thumbnail' => $thumbnail,
            'product_sku'       => $this->orderItem?->product?->sku,
            'product_variant'   => OrderService::getOrderItemVariantText($this->orderItem?->product_variant?->variations ?? []),
            'created_at'        => $this->created_at,
            'seller_reply_date' => $this->review_reply ? $this->updated_at : null,
            'can_update'        => $this->canReview(),
        ];
    }

    public function canReview(): bool
    {
        if (Carbon::parse($this->created_at)->lt(now()->subDays(3))) {
            return false;
        }

        return true;
    }
}
