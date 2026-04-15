<?php

namespace App\Filters\Product;

class RelatedProductFilter
{
    public function handle($query, $next)
    {
        if (request()->related_product_id) {
            $query->where('products.id', '!=', request()->related_product_id);
        }

        return $next($query);
    }
}
