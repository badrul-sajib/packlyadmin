<?php

namespace Modules\Api\V1\Ecommerce\Cart\Http\Resources;

use App\Enums\ProductAvailabilityStatus;
use App\Http\Resources\Ecommerce\ProductVariationResource;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class CartItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if ($this->product && isset($this->product->shopProduct)) {
            $availabilityStatus = $this->product->shopProduct->availabilityStatus($this->product_variation_id);
        } else {
            $availabilityStatus = ProductAvailabilityStatus::UNKNOWN;
        }

        $charges            = $this->product?->merchant?->getDeliveryCharges();

        return [
            'product_id'           => $this->product_id,
            'product_variation_id' => $this->product_variation_id,
            'is_selected'          => (bool) $this->is_selected,
            'availability'         => $availabilityStatus->value,
            'shop_id'              => $this->product?->merchant?->id,
            'shop_name'            => $this->product?->merchant?->shop_name,
            'quantity'             => $this->quantity,
            'weight'               => $this->product?->weight,
            'name'                 => $this->product?->name,
            'slug'                 => $this->product?->slug,
            'thumbnail'            => $this->product_variation_id ? ($this->variation?->image ?: $this->product?->thumbnail) : $this->product?->thumbnail,
            'current_stock'        => $this->product_variation_id ? $this->variation?->total_stock_qty : $this->product?->total_stock_qty,
            'max_cart_quantity'    => null,
            'regular_price'        => $this->regular_price,
            'brand_name'           => $this->product?->brand?->name            ?? null,
            'category'             => $this->product?->category                ?? null,
            'sub_category_name'    => $this->product?->subCategory?->name      ?? null,
            'child_category_name'  => $this->product?->subCategoryChild?->name ?? null,
            'discount_price'       => $this->discounted_price,
            'id_delivery_fee'      => Arr::get($charges, 'id_delivery_fee', '0'),
            'od_delivery_fee'      => Arr::get($charges, 'od_delivery_fee', '0'),
            'ed_delivery_fee'      => Arr::get($charges, 'ed_delivery_fee', '0'),
            'variation'            => $this->product_variation_id ? new ProductVariationResource($this->variation) : null,
            'badges'               => $this->product->badges->map(function ($badge) {
                return [
                    'id'           => intval($badge->id),
                    'name'         => $badge->name,
                    'type'         => intval($badge->type),
                    'type_label'   => $badge->type_label,
                ];
            }),
            'badgeProductVariationsExclude' => $this->product->badgeProductVariations->map(function ($variation) {
                return [
                    'variation_id' => intval($variation->product_variation_id),
                    'sku'          => $variation->productVariation->sku,
                    'variation'    => OrderService::getOrderItemVariantText($variation->productVariation->variations),
                ];
            }),
        ];
    }
}
