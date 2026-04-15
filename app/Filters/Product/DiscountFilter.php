<?php

namespace App\Filters\Product;

class DiscountFilter
{
    public function handle($query, $next)
    {
        $discount = request()->discount_percentage;

        if ($discount) {
            $query->whereRaw('(sp.e_price - sp.e_discount_price) / sp.e_price * 100 >= ?', [$discount]);
        }

        return $next($query);
    }
}
