<?php

namespace App\Filters\Product;

class MerchantFilter
{
    public function handle($query, $next)
    {
        if (request()->merchant_id) {
            $query->where('products.merchant_id', intval(request()->merchant_id));
        }

        return $next($query);
    }
}
