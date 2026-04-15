<?php

namespace App\Filters\Product;

class PriceRangeFilter
{
    public function handle($query, $next)
    {
        $min = request()->min_price;
        $max = request()->max_price;

        if ($min > 0 && $max > 0) {
            $query->whereBetween('sp.e_discount_price', [$min, $max]);
        }elseif ($min > 0) {
            $query->where('sp.e_discount_price', '>=', $min);
        }elseif ($max > 0) {
            $query->where('sp.e_discount_price', '<=', $max);
        }

        return $next($query);
    }
}
