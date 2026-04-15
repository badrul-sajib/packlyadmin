<?php

namespace Modules\Api\V1\Merchant\Reel\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReelResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                    => $this->id,
            'title'                 => $this->title,
            'link'                  => $this->link,
            'description'           => $this->description,
            'shop_id'               => $this->merchant_id,
            'shop_slug'             => $this->merchant?->slug,
            'shop_name'             => $this->merchant?->shop_name,
            'image_url'             => $this->image ? $this->image : $this->thumbnail_image,
            'video_url'             => $this->video,
            'enable_buy_now_button' => (bool) $this->enable_buy_now_button,
            'buy_now_type'          => $this->enable_buy_now_button ? $this->buy_now_type : null,
            'product_id'            => $this->enable_buy_now_button && $this->buy_now_type === 'product' ? $this->product_id : null,
            'reaction_count'        => $this->reaction_count,
            'view_count'            => $this->view_count ?? 0,
            'created_at'            => $this->created_at?->diffForHumans(),
        ];
    }
}
