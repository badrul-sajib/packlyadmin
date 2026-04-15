<?php

namespace App\Services;

use App\Models\Product\Product;
use App\Enums\ShopProductStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ShopSettingService
{
    public function getShopProducts($limit = null): LengthAwarePaginator|\Illuminate\Pagination\LengthAwarePaginator|array
    {
        $search      = request()->input('search', '');
        $perPage     = request()->input('per_page', '');
        $page        = request()->input('page', 1);
        $category_id = request()->input('category_id', '');

        // Get merchant's products with essential relations
        $productsQuery = Product::where('products.merchant_id', auth()->user()->merchant->id)
            ->whereHas('shopProduct', function ($query) {
                $query->where('status', ShopProductStatus::APPROVED->value);
            })
            ->with([
                'media',
                'productDetail:id,product_id,regular_price,discount_price,e_price,e_discount_price',
            ])
            ->leftJoin('shop_products', 'products.id', '=', 'shop_products.product_id')
            ->select(
                'products.id',
                'products.category_id',
                'products.name',
                'products.slug',
                'products.product_type_id',
                'shop_products.e_price',
                'shop_products.e_discount_price',
            );

        // Apply filters
        if ($search) {
            $productsQuery->where('products.name', 'like', '%'.$search.'%');
        }

        if ($category_id) {
            $productsQuery->where('products.category_id', $category_id);
        }
        if ($limit) {
            $productsQuery->limit($limit);
        }

        return $productsQuery->paginate($perPage, ['*'], 'page', $page);
    }

    public function getShopProductsByIds(array $productIds): LengthAwarePaginator|\Illuminate\Pagination\LengthAwarePaginator|array
    {
        $search      = request()->input('search', '');
        $perPage     = request()->input('per_page', '');
        $page        = request()->input('page', 1);
        $category_id = request()->input('category_id', '');

        // Get merchant's products with essential relations
        $productsQuery = Product::where('products.merchant_id', auth()->user()->merchant->id)
            ->whereIn('products.id', $productIds)
            ->where('products.status', 1)
            ->whereHas('shopProduct', function ($query) {
                $query->where('status', 2)
                    ->where('e_price', '>', 0);
            })
            ->with([
                'media',
                'productDetail:id,product_id,regular_price,discount_price,e_price,e_discount_price',
            ])
            ->leftJoin('shop_products', 'products.id', '=', 'shop_products.product_id')
            ->select(
                'products.id',
                'products.category_id',
                'products.name',
                'products.slug',
                'products.product_type_id',
                'shop_products.e_price',
                'shop_products.e_discount_price',
            )
            ->orderByRaw('FIELD(products.id, '.implode(',', $productIds).')');

        // Get paginated products
        return $productsQuery->paginate($perPage, ['*'], 'page', $page);
    }

    public function updateShopStatusSeen()
    {
        $merchant = auth()->user()->merchant;

        if ($merchant->shop_status_seen == 1) {
            return $merchant->update([
                'shop_status_seen' => 0,
            ]);
        }

        return $merchant->update([
            'shop_status_seen' => 1,
        ]);

    }

    public function updateShopAddress(array $data)
    {
        Auth::user()->merchant->update($data);
    }

}
