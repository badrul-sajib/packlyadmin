<?php

namespace App\Filters\Product;

class RatingFilter
{
    public function handle($query, $next)
    {
        if (request()->rating) {
            $query->where('products.total_rating', '>=', request()->rating);
        }

        return $next($query);
    }
}
