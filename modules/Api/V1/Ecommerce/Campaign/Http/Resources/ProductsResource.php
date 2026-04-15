<?php

namespace Modules\Api\V1\Ecommerce\Campaign\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $product = $this->shopProduct;

        return [
            'id'                            => intval($this->id),
            'name'                          => $this->name,
            'sku'                           => $this->sku ?? "",
            'slug'                          => $this->slug,
            'regular_price'                 => $product->e_price,
            'discount_price'                => discount_price($this->product_id, $product->e_price, $product->e_discount_price),
            'is_variant'                    => $this->is_variant,
            'thumbnail'                     => $this->thumbnail,
            'rating_avg'                    => (double) $this->total_rating,
            'rating_count'                  => (int) $this->total_review,
            'available_stock'               => intval($this->total_stock_qty),
            'shop_id'                       => intval($this->merchant_id),
            'shop_name'                     => $this->merchant->shop_name ?? '',
            'brand_name'                    => $this->brand->name ?? '',
            'category'                      => (object)[
                                                'id'    => intval($this->category_id),
                                                'name'  => $this->category->name,
                                                'slug'  => $this->category->slug,
                                            ],
            'sub_category_name'             => $this->subCategory->name ?? null,
            'sub_category_child_name'       => $this->subCategoryChild->name ?? null,
        ];
    }
}


