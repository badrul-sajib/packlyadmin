<?php

namespace App\Actions;

use App\Models\PrimeView\PrimeView;
use Exception;

class FetchPrimeViewProduct
{
    /**
     * @throws Exception
     */
    public static function execute($request)
    {
        // Validate and set limit
        $limit  = $request->input('limit', 20);
        $limit  = is_numeric($limit) && $limit > 0 ? min($limit, 30) : 10;
        $slug   = $request->input('slug');
        $search = $request->input('search', null);

        try {
            return PrimeView::where('status', 'active')
                ->with([
                    'media',
                    'products' => function ($query) use ($search) {
                        $query->baseShopQuery()
                            ->when($search, function ($query, $search) {
                                return $query->where('products.name', 'like', '%' . $search . '%');
                            });
                    },
                ])
                ->where('status', 'active')
                ->when($slug, function ($query) use ($slug) {
                    return $query->where('slug', $slug);
                })
                ->select('prime_views.id', 'prime_views.name', 'prime_views.slug', 'prime_views.start_date', 'prime_views.end_date')
                ->limit($limit)
                ->orderBy('order', 'asc')
                ->get();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
