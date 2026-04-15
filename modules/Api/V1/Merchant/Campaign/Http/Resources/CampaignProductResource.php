<?php

namespace Modules\Api\V1\Merchant\Campaign\Http\Resources;

use App\Enums\DiscountTypes;
use App\Models\Campaign\CampaignProduct;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CampaignProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $product = $this->product;

        return [
            'id' => $this->id,
            'product_id' => intval($product->id),
            'name' => $product->name,
            'slug' => $product->slug,
            'regular_price' => $product->shopProduct->e_price,
            'discount_price' => discount_price($product->id, $product->shopProduct->e_price, $product->shopProduct->e_discount_price),
            'campaign_discount_price' => $this->discountPrice($product->id, $product->shopProduct->e_price, $product->shopProduct->e_discount_price),
            'is_variant' => $product->is_variant,
            'thumbnail' => $product->thumbnail,
            'rating_avg' => $product->rating_avg,
            'rating_count' => $product->rating_count,
            'available_stock' => intval($product->total_stock_qty),
            'status' => $this->status,
            'status_label' => $this->status->label(),
        ];
    }

    public function discountPrice($productId, $regular_price, $discount_price): float
    {
        $campaignProduct = CampaignProduct::where('product_id', $productId)->first();

        // If this product is not in any campaign → return regular price
        if (!$campaignProduct) {
            return $discount_price;
        }

        $campaign = $campaignProduct->campaign;

        // If campaign is missing or invalid → return regular price
        if (!$campaign) {
            return $discount_price;
        }

        // Fetch discount info from prime view pivot
        $primeView = $campaign->campaignPrimeViews()
            ->where('prime_view_id', $campaignProduct->prime_view_id)
            ->first();
        // No discount configured → return regular price
        if (!$primeView) {
            return $discount_price;
        }

        $discountAmount = $primeView->discount_amount;
        $discountType = $primeView->discount_type;

        // Apply discount
        if ($discountType == DiscountTypes::PERCENTAGE->value) {
            $final = $regular_price - (($regular_price * $discountAmount) / 100);
        } else { // fixed discount
            $final = $regular_price - $discountAmount;
        }

        return max($final, 0);
    }
}
