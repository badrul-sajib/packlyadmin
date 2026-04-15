<?php
namespace App\Filters\Product;

class ChildCategoryFilter
{
    public function handle($query, $next)
    {
        if (request()->sub_category_child_id) {
            $query->where('products.sub_category_child_id', request()->sub_category_child_id);
        }

        return $next($query);
    }
}
