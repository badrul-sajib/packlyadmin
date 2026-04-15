<?php

namespace Modules\Api\V1\Ecommerce\Wishlist\Http\Resources;

use App\Models\Product\Product;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WishlistResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $variationId        = $this->product->productDetail?->default_variation_id;
        $availabilityStatus = $this->product->shopProduct->availabilityStatus($variationId);

        return [
            'id'                   => intval($this->product->id),
            'is_variant'           => $this->product->product_type_id == '2',
            'default_variation_id' => ($id = $this->product->productDetail?->default_variation_id) ? (int) $id : null,
            'availability'         => $availabilityStatus->value,
            'name'                 => $this->product->name,
            'slug'                 => $this->product->slug,
            'regular_price'        => $this->product->shopProduct->e_price,
            'discount_price'       => $this->product->shopProduct->e_discount_price,
            'thumbnail'            => $this->product->thumbnail,
            'rating_avg'           => $this->product->rating_avg,
            'rating_count'         => $this->product->rating_count,
            'available_stock'      => Product::currentProductStock($this->product_id, $this->product->productDetail?->default_variation_id),
            'badges'               => $this->product->badges->map(function ($badge) {
                return [
                    'id'         => intval($badge->id),
                    'name'       => $badge->name,
                    'type'       => intval($badge->type),
                    'type_label' => $badge->type_label,
                ];
            }),
            'badgeProductVariationsExclude' => $this->product?->badgeProductVariations->map(function ($variation) {
                return [
                    'variation_id' => intval($variation->product_variation_id),
                    'sku'          => $variation->productVariation->sku,
                    'variation'    => OrderService::getOrderItemVariantText($variation->productVariation->variations),
                ];
            }),
        ];
    }
}
