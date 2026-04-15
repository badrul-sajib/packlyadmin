<?php

namespace App\Actions;

use App\Models\Review\Review;

class ReviewList
{
    public function handle()
    {

        $search      = request('search', '');
        $perPage     = request('perPage', 10);
        $page        = request('page', 1);
        $merchantId  = request('merchant_id');
        $productId   = request('product_id');

        return Review::with([
            'user.media',
            'product:id,name,merchant_id,slug',
            'product.media',
            'orderItem.product_variant',
            'orderItem.merchant',
        ])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('product', function ($subQuery) use ($search) {
                        $subQuery->where('name', 'like', "%{$search}%");
                    })->orWhereHas('orderItem.merchant', function ($subQuery) use ($search) {
                        $subQuery->where('tracking_id', 'like', "%{$search}%");
                    });
                });
            })
            ->when($merchantId, function ($query) use ($merchantId) {
                $query->whereHas('product', function ($query) use ($merchantId) {
                    $query->where('merchant_id', $merchantId);
                });
            })
            ->when($productId, function ($query) use ($productId) {
                $query->where('product_id', $productId);
            })
            ->latest()->paginate($perPage, ['*'], 'page', $page)->withQueryString();
    }
}
