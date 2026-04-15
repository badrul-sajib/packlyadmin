<?php

namespace App\Services;

use App\Enums\ShopProductStatus;
use App\Models\Order\Cart;
use App\Models\Order\CartItem;
use App\Models\Product\Product;
use App\Models\Product\ProductVariation;
use App\Models\Shop\ShopProduct;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

class CartService
{
    public function getUserCart(): Cart
    {
        $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);
        $cart->items()
            ->whereDoesntHave('product', fn($q) => $q->whereNull('deleted_at'))
            ->delete();

        $cart->load([
            'items.product.merchant',
            'items.product.category',
            'items.product.brand',
            'items.product.badges',
            'items.product.subCategory',
            'items.product.subCategoryChild',
            'items.product.shopProduct',
            'items.variation',
            'items.variation.shopVariation',
            'items.product.variationAttributes.attributeOption.attribute',
        ]);

        foreach ($cart->items as $item) {
            if ($item->product_variation_id) {
                $variationAttributes = $item->product->variationAttributes
                    ->where('product_variation_id', $item->product_variation_id);

                $variationData = $variationAttributes->map(function ($va) {
                    return [
                        'attribute_name'   => $va->attributeOption?->attribute?->name,
                        'attribute_option' => $va->attributeOption?->attribute_value,
                    ];
                })->toArray();

                // Attach variationData to the cart item for use in the resource
                $item->variation_data = $variationData;
            }
        }

        return $cart;
    }

    public function bulkAddOrUpdateItems(array $items): array
    {
        if (empty($items)) {
            return [];
        }

        $failedItems = [];

        try {

            $cart = Cart::firstOrCreate(
                ['user_id' => Auth::id()],
                ['created_at' => now(), 'updated_at' => now()]
            );

            $cart->load('items');
            $existingItems = $cart->items;

            foreach ($items as $item) {
                try {
                    $this->validateCartItemData($item);

                    $action = $item['action'] ?? 'increase';

                    $cartItem = match ($action) {
                        'increase' => $this->handleIncrease($cart, $existingItems, $item),
                        'decrease' => $this->handleDecrease($existingItems, $item),
                        'set'      => $this->handleSet($cart, $existingItems, $item),
                        'select'   => $this->handleSelect($existingItems, $item),
                        default    => throw new \RuntimeException("Unsupported action: {$action}"),
                    };

                    // if is_select is explicitly passed, update the flag
                    if (isset($item['is_select']) && $cartItem) {
                        $cartItem->update(['is_selected' => $item['is_select']]);
                    }

                } catch (\Throwable $e) {
                    $failedItems[] = [
                        'product_id'           => $item['product_id']           ?? null,
                        'product_variation_id' => $item['product_variation_id'] ?? null,
                        'message'              => $e->getMessage(),
                    ];
                }
            }
            $cart->refresh();
        } catch (\Throwable $th) {
            return [];
        }

        return $failedItems;
    }

    private function validateCartItemData(array $item): void
    {
        if (! isset($item['product_id'])) {
            throw new InvalidArgumentException('product_id is required.');
        }
        $productExists = $this->productExists($item['product_id'], $item['product_variation_id'] ?? null);

        if ($productExists) {
            $exists = ShopProduct::where('product_id', $item['product_id'])
                ->where(function ($query) {
                    $query->where('active_status', 1)
                        ->where('status', ShopProductStatus::APPROVED);
                })
                ->exists();

            if (! $exists) {
                throw ValidationException::withMessages([
                    'product_id' => 'This product is not available in the shop.',
                ]);
            }
        }

        if ($this->isVariant($item['product_id']) && blank($item['product_variation_id'])) {
            throw ValidationException::withMessages([
                'product_variation_id' => 'Product variation ID is required for variant products.',
            ]);
        }

        if (! $productExists) {
            throw ValidationException::withMessages([
                'product_id' => 'Invalid product or variation ID.',
            ]);
        }

        $product = $this->findProduct($item['product_id'], $item['product_variation_id'] ?? null);

        if (! $product->merchant?->isActive()) {
            throw ValidationException::withMessages([
                'product_id' => 'The merchant shop is not active.',
            ]);
        }
    }

    private function handleIncrease(Cart $cart, Collection $existingItems, array $item): ?CartItem
    {
        $cartItem   = $this->findCartItem($existingItems, $item);
        $currentQty = $cartItem?->quantity ?? 0;
        $newQty     = $currentQty + $item['quantity'];

        if (! $this->checkStock($item['product_id'], $item['product_variation_id'] ?? null, $newQty)) {
            throw ValidationException::withMessages([
                'quantity' => "Insufficient stock to increase to {$newQty}.",
            ]);
        }

        if ($cartItem) {
            $cartItem->update([
                'quantity' => $newQty,
                'sku'      => $item['sku'] ?? null,
            ]);
        } else {
            $cartItem = $cart->items()->create([
                'product_id'           => $item['product_id'],
                'product_variation_id' => $item['product_variation_id'] ?? null,
                'sku'                  => $item['sku']                  ?? null,
                'quantity'             => $item['quantity'],
            ]);
        }

        return $cartItem;
    }

    private function handleDecrease(Collection $existingItems, array $item): ?CartItem
    {
        $cartItem = $this->findCartItem($existingItems, $item);

        if (! $cartItem) {
            throw ValidationException::withMessages([
                'product_id' => 'Item not found in cart to decrease.',
            ]);
        }

        $newQty = $cartItem->quantity - $item['quantity'];

        if ($newQty < 1) {
            throw ValidationException::withMessages([
                'quantity' => 'Cannot decrease below 1. Minimum quantity is 1.',
            ]);
        }

        $cartItem->update([
            'quantity' => $newQty,
        ]);

        return $cartItem;
    }

    private function handleSet(Cart $cart, Collection $existingItems, array $item): ?CartItem
    {
        $cartItem = $this->findCartItem($existingItems, $item);

        if (! $this->checkStock($item['product_id'], $item['product_variation_id'] ?? null, $item['quantity'])) {
            throw ValidationException::withMessages([
                'quantity' => "Insufficient stock to set quantity to {$item['quantity']}.",
            ]);
        }

        if ($cartItem) {
            $cartItem->update([
                'quantity' => $item['quantity'],
                'sku'      => $item['sku'] ?? null,
            ]);
        } else {
            $cartItem = $cart->items()->create([
                'product_id'           => $item['product_id'],
                'product_variation_id' => $item['product_variation_id'] ?? null,
                'sku'                  => $item['sku']                  ?? null,
                'quantity'             => $item['quantity'],
            ]);
        }

        return $cartItem;
    }

    private function handleSelect(Collection $existingItems, array $item): ?CartItem
    {
        $cartItem = $this->findCartItem($existingItems, $item);

        if (! $cartItem) {
            throw ValidationException::withMessages([
                'product_id' => 'Item not found in cart to select.',
            ]);
        }

        $cartItem->update(['is_selected' => $item['is_select']]);

        return $cartItem;
    }

    private function findCartItem(Collection $existingItems, array $item): ?CartItem
    {
        return $existingItems->first(
            fn ($cartItem) => $cartItem->product_id == $item['product_id'] &&
                $cartItem->product_variation_id     == ($item['product_variation_id'] ?? null)
        );
    }

    public function removeItems(array $items): void
    {
        $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);

        foreach ($items as $item) {
            $query = $cart->items()->where('product_id', $item['product_id']);

            if (isset($item['product_variation_id'])) {
                if ($item['product_variation_id'] !== null) {
                    $query->where('product_variation_id', $item['product_variation_id']);
                } else {
                    $query->whereNull('product_variation_id');
                }
            }

            $query->delete();
        }
    }

    private function checkStock(int $productId, ?int $variationId, int $quantity): bool
    {
        if ($variationId) {
            $variation = ProductVariation::where('id', $variationId)->where('product_id', $productId)->first();
            if (! $variation) {
                return false;
            }

            return $variation && $variation->total_stock_qty >= $quantity;
        }
        $product = Product::find($productId);

        if (! $product) {
            return false;
        }

        return $product->total_stock_qty >= $quantity;
    }

    private function findProduct(int $productId, ?int $variationId): ProductVariation|Product
    {
        if ($variationId) {
            return ProductVariation::where('id', $variationId)->where('product_id', $productId)->first();
        }

        return Product::where('id', $productId)->first();
    }

    private function productExists(int $productId, ?int $variationId): bool
    {
        if ($variationId) {
            return ProductVariation::where('id', $variationId)->where('product_id', $productId)->exists();
        }

        return Product::where('id', $productId)->exists();
    }

    private function isVariant($product_id): bool
    {
        $type = Product::where('id', $product_id)->value('product_type_id');

        return $type == 2;
    }
}
