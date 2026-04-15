<?php

namespace App\Services\Merchant\Product;

use App\Models\Product\Product;
use App\Models\Purchase\PurchaseDetail;
use App\Models\Stock\StockOrder;
use App\Services\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ProductVariationService
{
    public function fetchVariations(string $slug, $merchantId): JsonResponse
    {
        try {
            $product = $this->fetchProduct($slug, $merchantId);

            if (! $product->variations) {
                return ApiResponse::failure('No variations found.', Response::HTTP_NOT_FOUND);
            }

            $variationData = $this->getStockAndPurchaseData($product);
            $variations    = $this->formatVariations($product, $variationData['soldStockInventoryIds'], $variationData['purchasedVariationIds']);

            return ApiResponse::success('Product variations retrieved successfully.', [
                'variations' => $variations,
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ApiResponse::failure('Product not found.', Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return ApiResponse::failure('Product not found.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    protected function fetchProduct(string $slug, $merchantId): Product
    {
        return Product::with(['variations.variationAttributes', 'variations.stockInventory', 'variations.media', 'productDetail'])
            ->where([
                'slug'        => $slug,
                'merchant_id' => $merchantId,
            ])
            ->firstOrFail();
    }

    protected function getStockAndPurchaseData(Product $product): array
    {
        $stockInventoryIds = $product->variations->pluck('stockInventory.id')->filter()->toArray();

        $soldStockInventoryIds = StockOrder::whereIn('stock_inventory_id', $stockInventoryIds)
            ->whereNotNull('sell_product_detail_id')
            ->pluck('stock_inventory_id')
            ->toArray();

        $variationIds = $product->variations->pluck('id')->toArray();

        $purchasedVariationIds = PurchaseDetail::whereIn('variation_id', $variationIds)
            ->pluck('variation_id')
            ->toArray();

        Log::info('Sold Stock Inventory IDs: ', $soldStockInventoryIds);
        Log::info('Purchased Variation IDs: ', $purchasedVariationIds);

        return [
            'soldStockInventoryIds' => $soldStockInventoryIds,
            'purchasedVariationIds' => $purchasedVariationIds,
        ];
    }

    protected function formatVariations(Product $product, array $soldStockInventoryIds, array $purchasedVariationIds): \Illuminate\Support\Collection
    {
        return $product->variations->map(function ($variation) use ($soldStockInventoryIds, $purchasedVariationIds, $product) {
            Log::info('Processing Variation ID: '.$variation->id);

            $stockInventory = $variation->stockInventory;

            Log::info('Sold Stock Inventory IDs: '.in_array($stockInventory->id, $soldStockInventoryIds));
            Log::info('Purchased Variation IDs: '.in_array($variation->id, $purchasedVariationIds));

            $isSold           = in_array($stockInventory->id, $soldStockInventoryIds) || in_array($variation->id, $purchasedVariationIds);
            $defaultVariation = $variation->id == $product->productDetail->default_variation_id ? 1 : 0;

            return [
                'id'         => (int) $variation->id,
                'sku'        => $variation->sku,
                'barcode'    => $variation->barcode,
                'stock_qty'  => (int) $variation->total_stock_qty,
                'attributes' => $variation->variationAttributes->map(function ($variationAttribute) {
                    return [
                        'attribute_id'        => (int) $variationAttribute->attribute_id,
                        'attribute_option_id' => (int) $variationAttribute->attribute_option_id,
                    ];
                }),
                'purchase_price'        => $variation->purchase_price,
                'e_price'               => $variation->e_price,
                'e_discount_price'      => $variation->e_discount_price,
                'regular_price'         => $variation->regular_price,
                'discount_price'        => $variation->discount_price,
                'wholesale_price'       => $variation->wholesale_price,
                'wholesale_minimum_qty' => $variation->minimum_qty,
                'image'                 => $variation->image ?? null,
                'status'                => $variation->status,
                'is_default'            => (int) $defaultVariation,
                'is_sold'               => (int) $isSold,
            ];
        });
    }
}
