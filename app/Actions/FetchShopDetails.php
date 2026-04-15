<?php

namespace App\Actions;

use App\Models\Category\Category;
use App\Models\Merchant\Merchant;
use App\Models\Product\Product;
use Illuminate\Support\Facades\DB;

class FetchShopDetails
{
    public function execute($request, $id)
    {
        $merchant = Merchant::where('id', $id)->first();

        if (! $merchant) {
            return failure('Shop not found', 404);
        }

        $perPage     = $request->input('perPage', 10);
        $search      = $request->input('search');
        $sort        = $request->input('sort');
        $minPrice    = $request->input('min_price');
        $maxPrice    = $request->input('max_price');
        $category_id = $request->input('category_id');

        // Get merchant's products with essential relations
        $productsQuery = Product::where('merchant_id', $id)
            ->Active()
            ->with([
                'media',
                'shopProduct' => function ($query) {
                    $query->where('status', 2);
                },
                'category:id,name,slug',
                'productDetail:id,product_id,regular_price,discount_price',
            ])
            ->leftJoin('product_details', 'products.id', '=', 'product_details.product_id')
            ->select(
                'products.id',
                'products.category_id',
                'products.name',
                'products.slug',
                'products.product_type_id',
                'product_details.regular_price',
                'product_details.discount_price'
            );

        if ($search) {
            $productsQuery->where('products.name', 'like', '%'.$search.'%');
        }

        if ($category_id) {
            $productsQuery->where('products.category_id', $category_id);
        }

        if ($minPrice > 0 && $maxPrice > 0) {
            $productsQuery->whereBetween(DB::raw('CASE WHEN product_details.discount_price > 0 THEN product_details.discount_price ELSE product_details.regular_price END'), [$minPrice, $maxPrice]);
        } elseif ($minPrice > 0) {
            $productsQuery->where(DB::raw('CASE WHEN product_details.discount_price > 0 THEN product_details.discount_price ELSE product_details.regular_price END'), '>=', $minPrice);
        } elseif ($maxPrice > 0) {
            $productsQuery->where(DB::raw('CASE WHEN product_details.discount_price > 0 THEN product_details.discount_price ELSE product_details.regular_price END'), '<=', $maxPrice);
        }

        // Apply sorting
        if ($sort == 'low_price') {
            $productsQuery->orderBy(DB::raw('CASE WHEN product_details.discount_price > 0 THEN product_details.discount_price ELSE product_details.regular_price END'), 'ASC');
        } elseif ($sort == 'high_price') {
            $productsQuery->orderBy(DB::raw('CASE WHEN product_details.discount_price > 0 THEN product_details.discount_price ELSE product_details.regular_price END'), 'DESC');
        }

        $products = $productsQuery->paginate($perPage);

        $categories = Category::whereIn('id',
            Product::where('merchant_id', $id)
                ->Active()
                ->pluck('category_id')
                ->unique()
        )
            ->select('id', 'name', 'slug')
            ->get();

        return [
            'shop' => [
                'id'   => $merchant->id,
                'name' => $merchant->shop_name,
            ],
            'categories' => $categories->map(function ($category) {
                return [
                    'id'   => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                ];
            }),
            'products' => [
                'data' => $products->map(function ($product) {
                    return [
                        'id'       => $product->id,
                        'name'     => $product->name,
                        'slug'     => $product->slug,
                        'category' => [
                            'id'   => $product->category?->id,
                            'name' => $product->category?->name,
                            'slug' => $product->category?->slug,
                        ],
                        'regular_price'  => $product->regular_price           ?? 0,
                        'discount_price' => $product->discount_price          ?? 0,
                        'stock'          => $product->shopProduct->stock      ?? 0,
                    ];
                }),
                'per_page'      => $products->perPage(),
                'total'         => $products->total(),
                'current_page'  => $products->currentPage(),
                'next_page_url' => $products->nextPageUrl(),
                'last_page'     => $products->lastPage(),
            ],
        ];
    }
}
