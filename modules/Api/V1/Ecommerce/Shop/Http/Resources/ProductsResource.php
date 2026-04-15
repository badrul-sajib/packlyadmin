<?php

namespace Modules\Api\V1\Ecommerce\Shop\Http\Resources;

use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Services\OrderService;
use Illuminate\Support\Facades\Storage;
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
        $thumbnail = $this->thumbnail_path;
        if ($thumbnail) {
            $thumbnail = Storage::disk($this->getDiskName())->url($thumbnail);
        }

        return [
            'id'                            => intval($this->product_id),
            'name'                          => $this->name,
            'sku'                           => $this->sku ?? "",
            'slug'                          => $this->slug,
            'regular_price'                 => $this->e_price,
            'discount_price'                => $this->e_discount_price,
            'is_variant'                    => $this->is_variant,
            'thumbnail'                     => $thumbnail,
            'rating_avg'                    => (double) $this->total_rating,
            'rating_count'                  => (int) $this->total_review,
            'available_stock'               => intval($this->total_stock_qty),
            'shop_id'                       => intval($this->merchant_id),
            'shop_name'                     => $this->shop_name,
            'brand_name'                    => $this->brand_name ?? '',
            'category'                      => (object)[
                                                'id'    => intval($this->category_id),
                                                'name'  => $this->category_name,
                                                'slug'  => $this->category_slug,
                                            ],
            'sub_category_name'             => $this->sub_category_name ?? null,
            'sub_category_child_name'       => $this->child_category_name ?? null,
            'variations'                    => null,
            'badge_label'                   => $this->badge_label ?? null,
            'badges'                        => [],
            'badgeProductVariationsExclude' => [],
        ];
    }




}


