<?php

namespace App\Services\Checkout\Validators;

use App\DTOs\CheckoutData;
use App\Models\Product\Product;
use App\Models\Product\ProductVariation;
use Exception;

class StockValidator
{
    /**
     * @throws Exception
     */
    public function validate(CheckoutData $data): void
    {
        foreach ($data->productIds as $key => $productId) {
            $product  = Product::findOrFail($productId);
            $quantity = $data->quantities[$key] ?? throw new Exception('Product quantity not found');

            if ($product->product_type_id === 1) {
                $this->validateSimpleProductStock($product, $quantity);
            } elseif ($product->product_type_id === 2 && isset($data->skus[$key])) {
                $this->validateVariationStock($product, $data->skus[$key], $quantity);
            }
        }
    }

    /**
     * @throws Exception
     */
    private function validateSimpleProductStock(Product $product, int $quantity): void
    {
        if ($product->total_stock_qty < $quantity) {
            throw new Exception("Insufficient stock for product: {$product->name}");
        }
    }

    /**
     * @throws Exception
     */
    private function validateVariationStock(Product $product, string $sku, int $quantity): void
    {
        $variation = ProductVariation::where('product_id', $product->id)
            ->where('sku', $sku)
            ->first();

        if ($variation && $variation->total_stock_qty < $quantity) {
            throw new Exception("Insufficient stock for product: {$product->name}");
        }
    }
}
