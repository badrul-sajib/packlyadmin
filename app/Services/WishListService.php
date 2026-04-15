<?php

namespace App\Services;

use App\Models\Product\Product;
use App\Models\Product\Wishlist;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class WishListService
{
    public function getUserWishlist()
    {
        return Wishlist::with('product:id,name,slug,product_type_id')
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function toggleWishlistItem($userId, $productId): array
    {
        $this->validateProductExists($productId);

        $wishlistItem = Wishlist::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($wishlistItem) {
            $wishlistItem->delete();

            return [
                'message' => 'Product removed from wishlist',
                'data'    => ['action' => 'removed'],
            ];
        }

        $wishlistItem = Wishlist::create([
            'user_id'    => $userId,
            'product_id' => $productId,
        ]);

        return [
            'message' => 'Product added to wishlist',
            'data'    => [
                'action' => 'added',
                'item'   => $wishlistItem,
            ],
        ];
    }

    protected function validateProductExists($productId): void
    {
        if (! Product::where('id', $productId)->exists()) {
            throw new ModelNotFoundException;
        }
    }
}
