<?php

namespace App\Services\Coupon;

use App\Enums\CommonType;
use App\Models\Coupon\Coupon;
use App\Models\Product\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class CouponValidator
{
    public function __construct(
        private readonly Coupon $coupon,
        private readonly Product $product
    ) {}

    /**
     * Get active coupons with their relationships
     */
    private function getCoupons(?array $couponIds = null): Collection
    {
        return $this->coupon->where('end_date', '>=', now())
            ->with(['merchants', 'products', 'categories', 'brands'])
            ->withCount(['couponUsages' => function ($query) {
                $query->where('user_id', auth()->user()->id);
            }])
            ->when($couponIds, function ($query) use ($couponIds) {
                $query->whereIn('id', $couponIds);
            })
            ->get();
    }

    /**
     * Check if a product is eligible for a coupon based on various criteria
     */
    protected function isProductEligibleForCoupon(Coupon $coupon, Product $product): bool
    {
        // Merchant type validation
        if (! $this->validateMerchantType($coupon, $product)) {
            return false;
        }

        // Product type validation
        if (! $this->validateProductType($coupon, $product)) {
            return false;
        }

        // Category type validation
        if (! $this->validateCategoryType($coupon, $product)) {
            return false;
        }

        // Brand type validation
        if (! $this->validateBrandType($coupon, $product)) {
            return false;
        }

        return true;
    }

    /**
     * Validate merchant type rules
     */
    private function validateMerchantType(Coupon $coupon, Product $product): bool
    {
        $merchantIds = $coupon->merchants->pluck('id');

        if ($coupon->merchant_type === CommonType::EXCLUDE) {
            return ! $merchantIds->contains($product->merchant_id);
        }

        if ($coupon->merchant_type === CommonType::INCLUDE) {
            return $merchantIds->contains($product->merchant_id);
        }

        return true;
    }

    /**
     * Validate product type rules
     */
    private function validateProductType(Coupon $coupon, Product $product): bool
    {
        $productIds = $coupon->products->pluck('id');

        if ($coupon->product_type === CommonType::EXCLUDE) {
            return ! $productIds->contains($product->id);
        }

        if ($coupon->product_type === CommonType::INCLUDE) {
            return $productIds->contains($product->id);
        }

        return true;
    }

    /**
     * Validate category type rules
     */
    private function validateCategoryType(Coupon $coupon, Product $product): bool
    {
        $categoryIds = $coupon->categories->pluck('id');

        if ($coupon->category_type === CommonType::EXCLUDE) {
            return ! $categoryIds->contains($product->category_id);
        }

        if ($coupon->category_type === CommonType::INCLUDE) {
            return $categoryIds->contains($product->category_id);
        }

        return true;
    }

    /**
     * Validate brand type rules
     */
    private function validateBrandType(Coupon $coupon, Product $product): bool
    {
        $brandIds = $coupon->brands->pluck('id');

        if ($coupon->brand_type === CommonType::EXCLUDE) {
            return ! $brandIds->contains($product->brand_id);
        }

        if ($coupon->brand_type === CommonType::INCLUDE) {
            return $brandIds->contains($product->brand_id);
        }

        return true;
    }

    /**
     * Format coupon data for response
     */
    private function formatCouponResponse(Coupon $coupon, bool $isValid = true): array
    {
        $couponUsages = $coupon->coupon_usages_count;

        return [
            'id'                     => $coupon->id,
            'name'                   => $coupon->name,
            'code'                   => $coupon->code,
            'description'            => $coupon->description,
            'min_purchase'           => $coupon->min_purchase,
            'discount_value'         => $coupon->discount_value,
            'max_discount'           => $coupon->max_discount_value,
            'discount_type'          => $coupon->discount_type,
            'expires_at'             => $coupon->end_date,
            'is_valid'               => $isValid,
            'available_usages_limit' => $couponUsages ? ($coupon->usage_limit_per_user - $couponUsages) : $coupon->usage_limit_per_user,
        ];
    }

    public function getProductIdByCoupons(int $productId): Collection
    {
        $product = $this->product->findOrFail($productId);

        return $this->getCoupons()->filter(fn ($coupon) => $this->isProductEligibleForCoupon($coupon, $product) && $this->isUserEligibleForCoupon($coupon))
            ->map(fn ($coupon) => $this->formatCouponResponse($coupon))->values();
    }

    protected function isUserEligibleForCoupon(Coupon $coupon): bool
    {
        $coupon_usages = $coupon->coupon_usages_count;
        // $coupon_usages > $coupon->usage_limit_per_user
        if ($coupon_usages <= $coupon->usage_limit_per_user) {
            return true;
        }

        Log::info('User is not eligible for the coupon, usage limit exceeded');

        return false;
    }

    /**
     * Get eligible coupons for multiple products
     *
     * @throws InvalidArgumentException
     */
    public function getProductsByCoupons(array $productIds, array $couponIds): Collection
    {

        $products = $this->product->whereIn('id', $productIds)->get();
        $coupons  = $this->getCoupons($couponIds);

        return $coupons->map(function ($coupon) use ($products) {
            $isValid = $products->every(
                fn ($product) => $this->isProductEligibleForCoupon($coupon, $product)
            );

            return $this->formatCouponResponse($coupon, $isValid);
        });
    }
}
