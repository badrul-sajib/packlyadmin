<?php

namespace App\Filters\Product;


class PriceSortFilter
{
    public function handle($query, $next)
    {
        if (in_array(request()->get('sort'), ['low_price', 'high_price'])) {
            $order = request()->get('sort') === 'low_price' ? 'ASC' : 'DESC';
            $query->orderBy('sp.e_discount_price', $order);
        }
        
        return $next($query);
    }
}