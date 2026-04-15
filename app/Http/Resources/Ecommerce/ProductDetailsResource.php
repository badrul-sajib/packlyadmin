<?php

namespace App\Http\Resources\Ecommerce;

use App\Models\Merchant\MerchantSetting;
use App\Models\Product\Wishlist;
use App\Services\OrderService;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ProductDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = $request->user('sanctum');

        $is_followed = false;

        if ($user) {
            $is_followed = $user->followedMerchants()->where('merchant_id', $this->merchant->id)->exists();
        }

        $in_wishlist = false;
        if ($user) {
            $in_wishlist = Wishlist::where('user_id', $user->id)->where('product_id', $this->id)->exists();
        }

        $variationsWithStock = $this->variations->filter(function ($variation) {
            return $variation->stockInventory && $variation->stockInventory->stock_qty > 0;
        });

        $shopData = $this->merchant->settings->firstWhere('key', 'shop_settings');

        if (! $shopData) {
            $shopData = MerchantSetting::create([
                'merchant_id' => $this->merchant->id,
                'key'         => 'shop_settings',
                'value'       => json_encode([]),
            ]);
        }

        $settings = json_decode($shopData->value, true) ?? [];

        $warranty_type = null;

        if (! empty($this->warranty_type)) {
            $decoded = json_decode($this->warranty_type, true);
            // Check if valid JSON
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded) && count($decoded) > 0) {
                $warranty_type = $decoded;
            } else {
                $warranty_type = null;
            }
        }
        $charges = $this->merchant->getDeliveryCharges();

        $e_price          = $this->productDetail->e_price          ?? 0;
        $e_discount_price = $this->productDetail->e_discount_price ?? 0;

        // Only hit getProductPrice if both are 0 (or empty)
        if ((empty($e_price) || $e_price == 0) && (empty($e_discount_price) || $e_discount_price == 0)) {
            $productPrice = $this->getProductPrice($this);

            $e_price          = $productPrice->e_price          ?? 0;
            $e_discount_price = $productPrice->e_discount_price ?? 0;
        }

        return [
            'id'                 => intval($this->id),
            'name'               => $this->name,
            'slug'               => $this->slug,
            'sku'                => $this->sku,
            'thumbnail'          => $this->thumbnail,
            'images'             => $this->image,
            'description'        => $this->description,
            'weight'             => $this->weight ?? null,
            'category_id'        => intval($this->category_id),
            'category_info'      => $this->formatCategoryInfo($this),
            'brand_info'         => (object) [
                'id'   => $this->brand->id   ?? null,
                'name' => $this->brand->name ?? null,
            ],
            'specification'      => $this->specification,
            'warranty_note'      => $this->warranty_note ?? null,
            'has_warranty'       => $this->has_warranty ? 1 : 0,
            'warranty_type'      => $warranty_type, // $this->warranty_type ? json_decode($this->warranty_type) : null,
            'quantity'           => intval($this->total_stock_qty),
            'rating_avg'         => $this->rating_avg,
            'rating_count'       => $this->rating_count,
            'regular_price'      => $e_price,
            'discount_price'     => discount_price($this->id, $e_price, $e_discount_price),
            'id_delivery_fee'    => Arr::get($charges, 'id_delivery_fee', '0'),
            'od_delivery_fee'    => Arr::get($charges, 'od_delivery_fee', '0'),
            'ed_delivery_fee'    => Arr::get($charges, 'ed_delivery_fee', '0'),
            'shop_id'            => intval($this->merchant->id),
            'shop_name'          => $this->merchant->shop_name,
            'shop_slug'          => $this->merchant->slug == null ? ($this->merchant->shop_name !== null ? Str::slug($this->merchant->shop_name) : '') : $this->merchant->slug,
            'shop_image'         => isset($settings['shop_logo_and_cover']['shop_logo']['image']) ? $settings['shop_logo_and_cover']['shop_logo']['image'] : null,
            'is_followed'        => $is_followed,
            'shop_rating'        => $this->merchant->shop_rating,
            'ship_on_time'       => 95,
            'chat_response_time' => 88,
            'merchant_name'      => $this->merchant->name,
            'is_variant'         => $this->is_variant,
            'default_variant'    => $this->productDetail->selectedVariation->sku ?? null,
            'variations'         => ProductVariationResource::collection($this->variations()->where('e_price', '>', 0)->where('total_stock_qty', '>', 0)->where('status', 1)->get()),
            'attributes'         => ProductService::getAttributes($variationsWithStock),
            'badges'             => $this->badges->map(function ($badge) {
                return [
                    'id'         => intval($badge->id),
                    'name'       => $badge->name,
                    'type'       => intval($badge->type),
                    'type_label' => $badge->type_label,
                ];
            }),
            'badgeProductVariationsExclude' => $this->badgeProductVariations->map(function ($variation) {
                return [
                    'variation_id' => intval($variation->product_variation_id),
                    'sku'          => $variation->productVariation->sku,
                    'variation'    => OrderService::getOrderItemVariantText($variation->productVariation->variations),
                ];
            }),
            'in_wishlist'          => $in_wishlist,
            'review'               => (object) [
                'rating'   => $this->rating_avg,
                'count'    => $this->rating_count,
                'overview' => array_replace(
                    [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0],
                    $this->reviews->groupBy('rating')->map->count()->toArray()
                ),
            ],
            'question_count' => $this->comments()->whereNotNull('reply')->count(),
        ];
    }

    private function formatCategoryInfo($product): array
    {
        $categories = [];
        // Main Category
        if ($product->category_id) {
            $categories[] = array_filter([
                'category_id'   => $product->category_id,
                'name'          => $product->category->name ?? null,
                'slug'          => $product->category->slug ?? null,
            ]);
        }

        // Sub Category
        if ($product->sub_category_id) {
            $categories[] = array_filter([
                'sub_category_id'   => $product->sub_category_id,
                'name'              => $product->subCategory->name ?? null,
                'slug'              => $product->subCategory->slug ?? null,
            ]);
        }

        // Child Category
        if ($product->sub_category_child_id) {
            $categories[] = array_filter([
                'sub_category_child_id'   => $product->sub_category_child_id,
                'name'                    => $product->subCategoryChild->name ?? null,
                'slug'                    => $product->subCategoryChild->slug ?? null,
            ]);
        }

        return $categories;
    }

    public function getProductPrice($product)
    {
        $single = $product->product_type_id == 1; // single
        if ($single) {
            return $product->shopProduct;
        } else {
            return $product->productDetail->selectedVariation?->shopVariation;
        }
    }
}
