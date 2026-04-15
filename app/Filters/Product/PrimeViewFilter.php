<?php

namespace App\Filters\Product;

use Illuminate\Support\Facades\DB;

class PrimeViewFilter
{
    public function handle($query, $next)
    {
        $primeViewSlug = request()->prime_view_slug;
        if($primeViewSlug) {
            $query->whereHas('primeViews', function ($q) use ($primeViewSlug) {
                $q->where('slug', $primeViewSlug);
            });
        }
        return $next($query);
    }
}
