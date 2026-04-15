<?php

namespace App\Actions;

use App\Models\Product\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Pipeline\Pipeline;

class FetchShopProducts
{
    public function execute($request, $id, $productIds = null)
    {
        $perPage     = $request->input('perPage', 10);
        request()->merge(['merchant_id' => $id]);
        
        $filters = [
            \App\Filters\Product\CategoryFilter::class,
            \App\Filters\Product\PriceRangeFilter::class,
            \App\Filters\Product\MerchantFilter::class,
            \App\Filters\Product\PriceSortFilter::class,
            \App\Filters\Product\RelatedProductFilter::class,
            \App\Filters\Product\SearchFilter::class,
        ];

        $baseShopQuery = Product::baseShopQuery();    
        $query = app(Pipeline::class)
                ->send($baseShopQuery)
                ->through($filters)
                ->via('handle')
                ->thenReturn();

        $query->when($productIds, function ($q) use ($productIds) {
            $q->whereIn('products.id', $productIds)
                ->orderByRaw('FIELD(products.id, '.implode(',', $productIds).')');
        });

        return $query->paginate($perPage);
       
    }
}
