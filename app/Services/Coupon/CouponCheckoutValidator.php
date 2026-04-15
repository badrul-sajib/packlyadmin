<?php

namespace App\Services\Coupon;

use App\Caches\ShopSettingsCache;
use App\Enums\CommonType;
use App\Enums\CouponApplyOn;
use App\Models\Coupon\Coupon;
use App\Models\Order\CustomerAddress;
use App\Models\Product\Product;
use App\Models\Product\ProductVariation;
use App\Models\Shop\ShopProductVariation;
use App\Services\Checkout\Calculators\PriceCalculator;
use App\Services\Checkout\Calculators\ShippingCalculator;
use App\Support\CalculateWeightBasedCharge;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class CouponCheckoutValidator
{
    /**
     * @throws Exception
     */
    public function validate(array $data)
    {
        $userId = auth()->id();
        $code = $data['coupon_code'] ?? null;
        $customer_address_id = $data['customer_address_id'] ?? null;
        $orderItems = $this->calculatePrices($data);
        $coupon = Coupon::with(['products', 'couponUsages', 'merchants', 'categories', 'brands'])
            ->whereRaw('BINARY `code` = ?', [$code])
            ->first();

        if (! $coupon) {
            return null;
        }

        if ($coupon->apply_on == CouponApplyOn::SHIPPING_CHARGE->value && ! request()->has('customer_address_id')) {
            throw new Exception('Customer address is required for this coupon');
        }

        if (! $this->notExpired($coupon)) {
            throw new Exception('Coupon has expired');
        }

        $usedByUser = $this->usedByUser($coupon, $userId);

        if ($usedByUser >= $coupon->usage_limit_per_user) {
            throw new Exception('You crossed your maximum coupons limit');
        }

        $availableCoupons = ($coupon->usage_limit_per_user - $usedByUser) ?? 0;

        if ($coupon->apply_on == CouponApplyOn::SHIPPING_CHARGE->value) {
            return $this->validateShippingCharge($coupon, $orderItems, $customer_address_id);
        }

        $couponShopProducts = collect($this->couponEligibleForMerchant($coupon, $orderItems, $userId))
            ->filter(function ($shop) use ($coupon) {
                if ($coupon->bear_by_packly == 1) {
                    return true;
                }

                return $this->meetsMinimumPurchase($coupon, $shop->shop_amount);
            })
            ->sortByDesc('shop_amount')
            ->values()->take($availableCoupons);

        if ($couponShopProducts->isEmpty()) {
            throw new Exception('Coupon requirements are not fulfilled.');
        }

        $totalEligibleShopAmount = $couponShopProducts->sum('shop_amount');
        $couponDiscount = $this->calculateDiscountAmount($coupon, $totalEligibleShopAmount);

        if (! $this->meetsMinimumPurchase($coupon, $totalEligibleShopAmount)) {
            throw new Exception('Order total does not meet coupon minimum purchase requirement');
        }

        if ($coupon->bear_by_packly == 1) {
            $couponShopProducts = $couponShopProducts
                ->filter(function ($shop) {
                    return $shop->is_eligible_shop == true;
                })
                ->map(function ($shop) use ($couponDiscount, $totalEligibleShopAmount) {
                    $shop->shop_discounts = round(($shop->shop_amount / $totalEligibleShopAmount) * $couponDiscount, 2);

                    return $shop;
                });
        }

        return $couponShopProducts;
    }

    public function calculateDiscountAmount($coupon, $totalEligibleShopAmount): int
    {
        if ($coupon->discount_type == 'percentage') {
            return $shop_discounts = max(0, min($totalEligibleShopAmount * ($coupon->discount_value / 100), $coupon->max_discount_value));
        }

        return $coupon->discount_value;
    }

    public function validatePacklyCharge($coupon, $orderItems)
    {
        $total_price = 0;
        foreach ($orderItems as $products) {
            foreach ($products as $product) {
                $total_price += $product['total_price'] ?? 0;
            }
        }

        if ($coupon->min_purchase > $total_price) {
            throw new Exception('Order total does not meet coupon minimum purchase requirement');
        }

        return [
            'type' => CouponApplyOn::SHIPPING_CHARGE->value,
            'coupon_id' => $coupon->id,
            'total_amount' => $total_price,
        ];
    }

    public function validateShippingCharge($coupon, $orderItems, $customer_address_id)
    {
        $address = CustomerAddress::find($customer_address_id);
        if (! $address) {
            throw new Exception('Customer address is required for this coupon');
        }

        $shippingType = ShippingCalculator::determineShippingType($address);
        $shippingSettings = ShopSettingsCache::select(
            'shipping_fee_osd',
            'shipping_fee_isd',
            'shipping_additional_fee_isd',
            'shipping_additional_fee_osd',
        );

        $total_price = 0;
        $total_shipping_fee = 0;

        foreach ($orderItems as $products) {
            $itemsWeight = 0;
            foreach ($products as $product) {
                $total_price += $product['total_price'] ?? 0;
                $itemsWeight += ($product['quantity'] * $product['weight']) ?? 0;
            }

            $total_shipping_fee += CalculateWeightBasedCharge::run(
                totalWeight: $itemsWeight,
                shippingType: $shippingType,
                isd_fee: $shippingSettings->shipping_fee_isd,
                osd_fee: $shippingSettings->shipping_fee_osd,
                additional_isd_fee: $shippingSettings->shipping_additional_fee_isd,
                additional_osd_fee: $shippingSettings->shipping_additional_fee_osd,
            );
        }

        if ($coupon->min_purchase > $total_price) {
            throw new Exception('Order total does not meet coupon minimum purchase requirement');
        }

        if ($coupon->discount_type == 'percentage') {
            $discount_amount = $total_shipping_fee * $coupon->discount_value / 100;

            $total_discount_amount = $coupon->max_discount_value > $discount_amount ? $discount_amount : $coupon->max_discount_value;

            return [
                'type' => CouponApplyOn::SHIPPING_CHARGE->value,
                'bear_by_packly' => 1,
                'coupon_id' => $coupon->id,
                'total_amount' => $total_price,
                'discount_amount' => $total_discount_amount > $total_shipping_fee ? $total_shipping_fee : $total_discount_amount,
            ];
        }

        return [
            'type' => CouponApplyOn::SHIPPING_CHARGE->value,
            'bear_by_packly' => 1,
            'total_amount' => $total_price,
            'coupon_id' => $coupon->id,
            'discount_amount' => $coupon->discount_value > $total_shipping_fee ? $total_shipping_fee : $coupon->discount_value,
        ];
    }

    private function notExpired(Coupon $coupon): bool
    {
        return end_date($coupon->end_date) > start_date(now());
    }

    private function usedByUser(Coupon $coupon, $userId): int
    {
        return $coupon->couponUsages()->where('user_id', $userId)->count();
    }

    private function meetsMinimumPurchase(Coupon $coupon, $shop_amount): bool
    {
        return $shop_amount >= $coupon->min_purchase;
    }

    private function couponEligibleForMerchant(Coupon $coupon, $orderItems, $userId): array
    {
        $shopCouponProducts = [];

        foreach ($orderItems as $shopId => $products) {

            $shop_discounts = 0;
            $couponProducts = [];
            $hasEligibleProducts = false;
            $shopEligibleAmount = 0;

            if (empty($products)) {
                continue;
            }

            foreach ($products as $productDetails) {
                $product = Product::find($productDetails['product_id']);

                // Clean up unnecessary fields
                unset($productDetails['regular_price']);

                if (! $this->isProductEligibleForCoupon($coupon, $product)) {
                    $productDetails['is_eligible'] = false;
                    $productDetails['discount_amount'] = 0;
                    $productDetails['discount_percentage'] = 0;
                    $couponProducts[] = (object) $productDetails;

                    continue;
                }

                $hasEligibleProducts = true;
                $shopEligibleAmount += $productDetails['total_price'];

                $productDetails['is_eligible'] = true;
                $productDetails['discount_amount'] = 0;
                $productDetails['discount_percentage'] = 0;
                $couponProducts[] = (object) $productDetails;
            }

            // Calculate total discount amount
            if ($coupon->bear_by_packly != 1) {
                if ($coupon->discount_type == 'percentage') {
                    $shop_discounts = max(0, min($shopEligibleAmount * ($coupon->discount_value / 100), $coupon->max_discount_value));
                } else {
                    $shop_discounts = $coupon->discount_value;
                }
            }

            $shop_discounts = round($shop_discounts, 2);
            $remaining_discount = $shop_discounts;
            $last_product_index = count($couponProducts) - 1;

            foreach ($couponProducts as $key => $product) {
                if ($product->is_eligible) {
                    if ($key == $last_product_index) {
                        $couponProducts[$key]->discount_amount = number_format($remaining_discount, 2, '.', '');
                    } else {
                        $calculated_discount = ($product->total_price / $shopEligibleAmount) * $shop_discounts;
                        $couponProducts[$key]->discount_amount = number_format($calculated_discount, 2, '.', '');
                        $remaining_discount -= $product->discount_amount;
                    }
                    $couponProducts[$key]->discount_percentage = number_format(($product->total_price / $shopEligibleAmount) * 100, 2, '.', '');
                }
            }

            if ($hasEligibleProducts) {
                $shopCouponProducts[] = (object) [
                    'coupon_id' => $coupon->id,
                    'shop_id' => $shopId,
                    'shop_amount' => $shopEligibleAmount ?? 0,
                    'is_eligible_shop' => true,
                    'shop_discounts' => $shop_discounts,
                    'bear_by_packly' => $coupon->bear_by_packly,
                    'products' => collect($couponProducts)->toArray(),
                ];
            }
        }

        return $shopCouponProducts;
    }

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
     * @throws Exception
     */
    private function calculatePrices(array $data): array
    {
        $orderItems = [];
        foreach ($data['product_id'] as $key => $productId) {
            $product = Product::with('shopProduct')
                ->findOrFail($productId);
            $quantity = $data['quantity'][$key] ?? throw new Exception('Product quantity not found');

            $priceData = $this->calculateProductPrice($product, $data['sku'][$key] ?? null);

            $orderItems[$product->merchant_id][] = [
                'product_id' => $product->id,
                'product_variation_id' => $priceData['variation_id'],
                'regular_price' => $product->shopProduct->regular_price,
                'price' => $priceData['price'],
                'total_price' => $priceData['price'] * $quantity,
                'quantity' => $quantity,
                'sku' => $data['sku'][$key] ?? null,
                'weight' => $product->weight ?? 0,
            ];
        }

        return $orderItems;
    }

    private function calculateProductPrice(Product $product, ?string $sku): array
    {
        $basePrice = $product->productDetail()
            ->select(DB::raw('CASE WHEN e_discount_price > 0 THEN e_discount_price ELSE e_price END AS price'))
            ->first()->price;

        if (! $sku) {
            return ['price' => $basePrice, 'variation_id' => null];
        }

        $variation = ProductVariation::where('product_id', $product->id)
            ->where('sku', $sku)
            ->select('id')
            ->first();

        $shopVariation = ShopProductVariation::where('product_id', $product->id)
            ->where('product_variation_id', $variation->id)
            ->select(DB::raw('id, CASE WHEN e_discount_price > 0 THEN e_discount_price ELSE e_price END AS price'))
            ->first();

        return $shopVariation
            ? ['price' => $shopVariation->price, 'variation_id' => $variation->id]
            : ['price' => $basePrice, 'variation_id' => null];
    }

    public static function getMerchantCoupons(int $merchantId): Collection
    {
        return Coupon::query()
            // Only active and not expired
            ->where('end_date', '>', now())
            ->where('status', 'active')
            ->where(function ($query) use ($merchantId) {
                $query
                    // merchant_type = 1 → Exclude coupon if merchant is listed
                    ->where(function ($q) use ($merchantId) {
                        $q->where('merchant_type', '1')
                            ->whereDoesntHave('merchants', function ($subQ) use ($merchantId) {
                                $subQ->where('merchant_id', $merchantId);
                            });
                    })
                    // merchant_type = 2 → Include coupon only if merchant is listed
                    ->orWhere(function ($q) use ($merchantId) {
                        $q->where('merchant_type', '2')
                            ->whereHas('merchants', function ($subQ) use ($merchantId) {
                                $subQ->where('merchant_id', $merchantId);
                            });
                    });
            })
            ->get();
    }

    public static function getShippingFee($address_id, array $orderItems, array $deliveryType)
    {
        $address = CustomerAddress::find($address_id);
        $shippingType = ShippingCalculator::determineShippingType($address);
        $merchantShippingDetails = [];
        $totalShippingFee = 0;

        foreach ($orderItems as $merchantId => $items) {
            $maxDeliveryCharge = 0;
            $hasNonFreeShipping = false;

            $isExpressDelivery = ($deliveryType[$merchantId] ?? null) == 2;

            foreach ($items as $product) {
                $product_data = Product::with('shopProduct')->findOrFail($product['product_id']);
                $free_shipping = PriceCalculator::checkFreeShipping($product_data, $product['product_variation_id'] ?? null);

                if ($isExpressDelivery || ! $free_shipping) {
                    $hasNonFreeShipping = true;

                    $deliveryCharge = ShippingCalculator::getProductDeliveryCharge(
                        $product['product_id'],
                        $product['product_variation_id'],
                        $shippingType,
                        $deliveryType[$merchantId] ?? 1
                    );

                    $maxDeliveryCharge = max($maxDeliveryCharge, $deliveryCharge);
                }
            }

            $merchantShippingDetails[$merchantId] = [
                'has_free_shipping' => ! $hasNonFreeShipping,
                'delivery_charge' => $hasNonFreeShipping ? $maxDeliveryCharge : 0,
                'delivery_type' => $deliveryType[$merchantId] ?? 1,
            ];

            if ($hasNonFreeShipping) {
                $totalShippingFee += $maxDeliveryCharge;
            }
        }

        return $totalShippingFee;

    }
}
