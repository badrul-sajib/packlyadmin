<?php

namespace App\Services\Voucher;

use App\Enums\CommonType;
use App\Models\Product\Product;
use App\Models\Voucher\Voucher;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class VoucherValidator
{
    public function __construct(
        private readonly Voucher $voucher,
        private readonly Product $product
    ) {}

    /**
     * Get active vouchers with their relationships
     */
    private function getVouchers(?array $voucherIds = null): Collection
    {
        return $this->voucher->where('end_date', '>=', now())
            ->when($voucherIds, function ($query) use ($voucherIds) {
                $query->whereIn('id', $voucherIds);
            })
            ->with(['merchants', 'products', 'categories', 'brands'])
            ->get();
    }

    /**
     * Check if a product is eligible for a voucher based on various criteria
     */
    private function isProductEligibleForVoucher(Voucher $voucher, Product $product): bool
    {
        // Merchant type validation
        if (! $this->validateMerchantType($voucher, $product)) {
            return false;
        }

        // Product type validation
        if (! $this->validateProductType($voucher, $product)) {
            return false;
        }

        // Category type validation
        if (! $this->validateCategoryType($voucher, $product)) {
            return false;
        }

        // Brand type validation
        if (! $this->validateBrandType($voucher, $product)) {
            return false;
        }

        return true;
    }

    /**
     * Validate merchant type rules
     */
    private function validateMerchantType(Voucher $voucher, Product $product): bool
    {
        $merchantIds = $voucher->merchants->pluck('id');

        if ($voucher->merchant_type === CommonType::EXCLUDE) {
            return ! $merchantIds->contains($product->merchant_id);
        }

        if ($voucher->merchant_type === CommonType::INCLUDE) {
            return $merchantIds->contains($product->merchant_id);
        }

        return true;
    }

    /**
     * Validate product type rules
     */
    private function validateProductType(Voucher $voucher, Product $product): bool
    {
        $productIds = $voucher->products->pluck('id');

        if ($voucher->product_type === CommonType::EXCLUDE) {
            return ! $productIds->contains($product->id);
        }

        if ($voucher->product_type === CommonType::INCLUDE) {
            return $productIds->contains($product->id);
        }

        return true;
    }

    /**
     * Validate category type rules
     */
    private function validateCategoryType(Voucher $voucher, Product $product): bool
    {
        $categoryIds = $voucher->categories->pluck('id');

        if ($voucher->category_type === CommonType::EXCLUDE) {
            return ! $categoryIds->contains($product->category_id);
        }

        if ($voucher->category_type === CommonType::INCLUDE) {
            return $categoryIds->contains($product->category_id);
        }

        return true;
    }

    /**
     * Validate brand type rules
     */
    private function validateBrandType(Voucher $voucher, Product $product): bool
    {
        $brandIds = $voucher->brands->pluck('id');

        if ($voucher->brand_type === CommonType::EXCLUDE) {
            return ! $brandIds->contains($product->brand_id);
        }

        if ($voucher->brand_type === CommonType::INCLUDE) {
            return $brandIds->contains($product->brand_id);
        }

        return true;
    }

    /**
     * Format voucher data for response
     */
    private function formatVoucherResponse(Voucher $voucher, bool $isValid = true): array
    {
        $voucher_usages = DB::table('voucher_usages')->where('user_id', auth()->user()->id)->where('voucher_id', $voucher->id)->count();

        return [
            'id'                     => $voucher->id,
            'name'                   => $voucher->name,
            'code'                   => $voucher->code,
            'description'            => $voucher->description,
            'min_purchase'           => $voucher->min_purchase,
            'discount_value'         => $voucher->discount_value,
            'max_discount'           => $voucher->max_discount_value,
            'discount_type'          => $voucher->discount_type,
            'expires_at'             => $voucher->end_date,
            'is_valid'               => $isValid,
            'available_usages_limit' => $voucher_usages ? ($voucher->usage_limit_per_user - $voucher_usages) : $voucher->usage_limit_per_user,
        ];
    }

    /**
     * Get eligible vouchers for a single product
     */
    public function getProductIdByVouchers(int $productId): Collection
    {
        $product = $this->product->findOrFail($productId);
        $user_id = auth()->user()->id;

        return $this->getVouchers()->filter(fn ($voucher) => $this->isProductEligibleForVoucher($voucher, $product) && $this->isUserEligibleForVoucher($voucher))
            ->map(fn ($voucher) => $this->formatVoucherResponse($voucher))->values();
    }

    protected function isUserEligibleForVoucher(Voucher $voucher): bool
    {
        $voucher_usages = DB::table('voucher_usages')->where('user_id', auth()->user()->id)->where('voucher_id', $voucher->id)->count();
        // $voucher_usages > $voucher->usage_limit_per_user
        if ($voucher_usages <= $voucher->usage_limit_per_user) {
            return true;
        }

        return false;
    }

    /**
     * Get eligible vouchers for multiple products
     *
     * @throws InvalidArgumentException
     */
    public function getProductsByVouchers(array $productIds, array $voucherIds): Collection
    {
        $products = $this->product->whereIn('id', $productIds)->get();
        $vouchers = $this->getVouchers($voucherIds);

        return $vouchers->map(function ($voucher) use ($products) {
            $isValid = $products->every(
                fn ($product) => $this->isProductEligibleForVoucher($voucher, $product)
            );

            return $this->formatVoucherResponse($voucher, $isValid);
        });
    }
}
