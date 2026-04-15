<?php

namespace App\Filters\Product;

class CategoryFilter
{
    public function handle($query, $next)
    {
        if (request()->category_id) {
            $query->where('products.category_id', request()->category_id);
        }

        return $next($query);
    }
}
