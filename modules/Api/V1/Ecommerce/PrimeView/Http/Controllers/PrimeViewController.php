<?php

namespace Modules\Api\V1\Ecommerce\PrimeView\Http\Controllers;

use App\Actions\FetchPrimeViewProduct;
use App\Enums\ShopProductStatus;
use App\Http\Controllers\Controller;
use Modules\Api\V1\Ecommerce\PrimeView\Http\Resources\PrimeViewProductResource;
use Modules\Api\V1\Ecommerce\PrimeView\Http\Resources\TrendingCategoryResource;
use App\Models\Category\Category;
use App\Models\PrimeView\PrimeView;
use App\Models\Product\Product;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PrimeViewController extends Controller
{
    public function primeView(Request $request)
    {
        try {
            $prime_views = FetchPrimeViewProduct::execute($request);

            return success('Prime View showed successfully', PrimeViewProductResource::collection($prime_views));
        } catch (Exception $e) {
            return failure('Prime View not found', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function stickyMenu()
    {
        try {
            $stickyMenu = PrimeView::where('show_on_sticky', 1)->where('status', 'active')->has('primeViewProducts')->get();

            return success('Sticky Menu showed successfully', $stickyMenu);
        } catch (Exception $e) {
            return failure('Sticky Menu not found', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function exploreItem()
    {
        try {
            $stickyMenu = PrimeView::withCount(['products' => function($query){
                $query->whereNull('deleted_at')
                ->join('shop_products as sp', function ($q) {
                    $q->on('sp.product_id', '=', 'products.id')
                    ->where('sp.status', 2)
                    ->where('sp.e_discount_price', '>', 0)
                    ->where('sp.e_price', '>', 0);
                });
            }])
            ->where('explore_item', 1)->where('status', 'active')->get()->map(function ($primeView){
                $primeView->prime_view_products_count = $primeView->products_count;
                return $primeView;
            });

            return success('Explore Item showed successfully', $stickyMenu);
        } catch (Exception $e) {
            return failure('Explore Item not found', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function trendingCategoryHeader(Request $request)
    {
        try {
            $slug = $request->get('slug', 'trending-products');

            $categoryIds = Product::where('status', 1)
                ->whereHas('primeViews', function ($query) use ($slug) {
                    $query->where('slug', $slug);
                })
                ->whereHas('shopProduct', function ($query) {
                    $query->where('status', ShopProductStatus::APPROVED->value)
                        ->where('e_price', '>', 0);
                })
                ->pluck('category_id')
                ->unique()
                ->filter();

            $trendingCategories = Category::whereIn('id', $categoryIds)->get();

            return success('Trending Categories showed successfully', TrendingCategoryResource::collection($trendingCategories));
        } catch (Exception $e) {
            return failure('Trending Categories not found', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
