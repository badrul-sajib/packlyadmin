<?php

namespace App\Actions;

use App\Http\Resources\Ecommerce\CouponResource;
use App\Http\Resources\Ecommerce\ProductsResource;
use App\Models\Category\Category;
use App\Models\Merchant\Merchant;
use App\Models\Merchant\MerchantSetting;
use App\Models\Merchant\MerchantSlug;
use App\Models\Product\Product;
use App\Services\Coupon\CouponCheckoutValidator;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class FetchShopBasicDetails
{
    public function execute(string|int $idOrSlug): JsonResponse
    {
        $merchant = is_numeric($idOrSlug)
            ? Merchant::active()->where('id', (int) $idOrSlug)->first()
            : Merchant::active()->where('slug', $idOrSlug)->first();

        if (! $merchant) {
            $fallbackMerchant = MerchantSlug::where('slug', $idOrSlug)->latest()->first();
            if ($fallbackMerchant) {
                $merchant = Merchant::active()->where('id', $fallbackMerchant->merchant_id)->first();
            } else {
                return failure('Shop not found', Response::HTTP_NOT_FOUND);
            }
        }

        $user = request()->user('sanctum');

        $isFollowed = false;

        if ($user) {
            $isFollowed = $user->followedMerchants()->where('merchant_id', $merchant->id)->exists();
        }
        $categories = Category::whereIn(
            'id',
            Product::where('merchant_id', $merchant->id)
                ->Active()
                ->pluck('category_id')
                ->unique()
        )
            ->select('id', 'name', 'slug')
            ->get();

        $updated_settings = MerchantSetting::where('merchant_id', $merchant->id)
            ->where('key', 'shop_settings')
            ->first();

        if (! $updated_settings) {
            $updated_settings = MerchantSetting::create([
                'merchant_id' => $merchant->id,
                'key'         => 'shop_settings',
                'value'       => json_encode([]),
            ]);
        }

        $shop_settings = json_decode($updated_settings?->value, true);
        $shop_settings = ! empty($shop_settings) ? $shop_settings : null;

        //  resolve product IDs to actual product data in product highlights
        $shop_settings = $this->resolveProductIdsToData(shopSettings: $shop_settings, merchantId: $merchant->id);
       
       
        $data = [
            'shop' => [
                'id'              => $merchant->id,
                'name'            => $merchant->shop_name,
                'is_followed'     => $isFollowed,
                'followers_count' => $merchant->followers()->count(),
            ],
            'categories' => $categories->map(function ($category) {
                return [
                    'id'    => $category->id,
                    'name'  => $category->name,
                    'slug'  => $category->slug,
                    'image' => $category->image,
                ];
            }),
            'ship_on_time'          => 95,
            'chat_response_time'    => 88,
            'shop_rating'           => $this->shop_rating($merchant),
            'joined_date'           => $merchant->created_at?->diffForHumans(),
            'trusted_shop'          => 100,
            'prime_views'           => $merchant->prime_views,
            'shop_settings'         => $shop_settings,
            'coupons'               => CouponResource::collection(CouponCheckoutValidator::getMerchantCoupons($merchant->id)),
        ];

        return success('Shop basic details fetched successfully', $data, Response::HTTP_OK, ['unique_id','slug']);
    }

    public function shop_rating(Merchant $merchant): string
    {
        $merchant->load('products.reviews');
        $reviews = $merchant->products->pluck('reviews')->flatten();

        if ($reviews->isEmpty()) {
            return '0.00';
        }

        $averageSellerRating   = $reviews->avg('seller_rating');
        $averageShippingRating = $reviews->avg('shipping_rating');
        $averageGeneralRating  = $reviews->avg('rating');

        $combinedAverage = ($averageSellerRating + $averageShippingRating + $averageGeneralRating) / 3;

        // Convert to percentage (5.0 = 100%, 1.0 = 20%)
        $percentage = ($combinedAverage / 5) * 100;

        return number_format($percentage, 2);
    }

    public function resolveProductIdsToData(?array $shopSettings, int $merchantId): ?array
    {
        if (blank($shopSettings)) {
            return $shopSettings;
        }

        $fetchShopProducts = new FetchShopProducts;

        if (! empty($shopSettings['product_highlights']['products'])) {

            $productIds = $shopSettings['product_highlights']['products'];

            $products = ProductsResource::collection($fetchShopProducts->execute(request(), $merchantId, $productIds));

            $shopSettings['product_highlights']['products'] = $products;
        }

        return $shopSettings;
    }
}
