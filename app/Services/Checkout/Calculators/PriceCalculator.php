<?php

namespace App\Services\Checkout\Calculators;

use App\DTOs\CheckoutData;
use App\Enums\BadgeType;
use App\Models\Product\Product;
use App\Models\Product\ProductVariation;
use App\Models\Shop\ShopProductVariation;
use Exception;
use Illuminate\Support\Facades\DB;

class PriceCalculator
{
    /**
     * @throws Exception
     */
    public function calculatePrices(CheckoutData $data): array
    {
        $orderItems = [];
        foreach ($data->productIds as $key => $productId) {

            $product = Product::with('shopProduct')->available()->where('id', $productId)->first();

            if (! $product) {
                throw new Exception('Product not available');
            }

            $quantity = $data->quantities[$key] ?? throw new Exception('Product quantity not found');

            $priceData = $this->calculateProductPrice($product, $data->skus[$key] ?? null);

            $checkFreeShipping = $this->checkFreeShipping($product, $priceData['variation_id'] ?? null);

            $orderItems[$product->merchant_id][] = [
                'product_id'           => $product->id,
                'product_variation_id' => $priceData['variation_id'],
                'regular_price'        => $priceData['regular_price'],
                'price'                => $priceData['price'],
                'quantity'             => $quantity,
                'free_shipping'        => $checkFreeShipping,
                'product_data'         => $product,
                'variation_data'       => $priceData['variation_id']
                    ? ShopProductVariation::find($priceData['variation_id'])
                    : null,
            ];
        }

        return $orderItems;
    }

    private function calculateProductPrice(Product $product, ?string $sku): array
    {
        $shopProduct = $product->shopProduct()
            ->select(DB::raw('CASE WHEN e_discount_price > 0 THEN e_discount_price ELSE e_price END AS price'), 'regular_price')
            ->first();

        if (! $sku) {
            return ['price' => $shopProduct->price, 'variation_id' => null, 'regular_price' => $shopProduct->regular_price];
        }

        $variation = ProductVariation::where('product_id', $product->id)
            ->where('sku', $sku)
            ->select(DB::raw('id, CASE WHEN e_discount_price > 0 THEN e_discount_price ELSE e_price END AS price'))
            ->first();

        $shopProductVariation = ShopProductVariation::where('product_id', $product->id)
            ->where('product_variation_id', $variation->id)
            ->select(DB::raw('id, CASE WHEN e_discount_price > 0 THEN e_discount_price ELSE e_price END AS price'), 'regular_price')
            ->first();

        return $variation
            ? ['price' => $shopProductVariation->price, 'regular_price' => $shopProductVariation->regular_price, 'variation_id' => $variation->id]
            : ['price' => $shopProduct->price, 'regular_price' => $shopProduct->regular_price, 'variation_id' => null];
    }

    public static function checkFreeShipping(Product $product, $variationId = null): bool
    {
        $badge = $product->badges()->where('status', '1')->where('type', BadgeType::FREE_SHIPPING->value)->first();
        if ($badge) {
            if (count($product->badgeProductVariations) > 0) {
                return $product->badgeProductVariations->where('product_variation_id', $variationId)->isEmpty();
            }

            return true;
        }

        return false;
    }
}
