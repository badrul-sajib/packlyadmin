<?php


namespace App\Filters\Product;

use App\Services\OpenSearchService;
use Illuminate\Support\Facades\Log;

class SearchFilter
{
    public function handle($query, $next)
    {
        $search = request()->search;
        $productIds = [];

        if ($search) {
            try {
                $search = strtolower($search ?? '');
                $results = (new OpenSearchService)->search('products', $search);
                $hits = $results['hits']['hits'] ?? [];
                $formatted = array_map(fn($hit) => $hit['_source'], $hits);
                $productIds =  collect($formatted)->pluck('id')->toArray();
            } catch (\Throwable $th) {
                Log::error("Failed to search products in OpenSearch: " . $e->getMessage());
                $productIds = [];  
            }
            if(count($productIds) > 0) {
                $query->whereIn('products.id', $productIds);
                $query->orderByRaw("FIELD(products.id, " . implode(',', $productIds) . ")");
            }
        }

        return $next($query);
    }
}
