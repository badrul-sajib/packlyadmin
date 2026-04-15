<?php

namespace App\Filters\Product;

class SubCategoryFilter
{
    public function handle($query, $next)
    {
        if (request()->sub_category_id) {
            $query->where('products.sub_category_id', request()->sub_category_id);
        }

        return $next($query);
    }
}
