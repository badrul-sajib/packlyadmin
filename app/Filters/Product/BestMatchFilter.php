<?php

namespace App\Filters\Product;


class BestMatchFilter
{
    public function handle($query, $next)
    {
        $sort = request()->sort;
         if($sort === 'best_match' && !request()->has('search')){
            $query->orderBy('products.total_rating', 'desc')->orderBy('products.total_review', 'desc');
        }
        return $next($query);
    }
}