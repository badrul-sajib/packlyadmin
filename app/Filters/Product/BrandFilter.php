<?php

namespace App\Filters\Product;

class BrandFilter
{
    public function handle($query, $next)
    {
        $brandIds = request()->brand_ids;

        if ($brandIds) {
            $ids = array_filter(array_map('trim', explode(',', $brandIds)));
            if (!empty($ids)) {
                $query->whereIn('products.brand_id', $ids);
            }
        }

        return $next($query);
    }
}
