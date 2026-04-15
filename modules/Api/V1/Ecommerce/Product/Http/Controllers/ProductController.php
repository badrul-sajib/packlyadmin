<?php

namespace Modules\Api\V1\Ecommerce\Product\Http\Controllers;

use App\Http\Controllers\Controller;
use AWS\CRT\HTTP\Response;
use Modules\Api\V1\Ecommerce\Product\Http\Resources\ProductDetailsResource;
use Modules\Api\V1\Ecommerce\Product\Http\Resources\ProductsResource;
use App\Services\ProductService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    public function __construct(protected ProductService $productService) {}


    public function productDetails($slug): JsonResponse
    {
        $product = ProductService::getProductBySlug($slug);

        return success('show product details', $product);
    }

    public function productVariant($slug): JsonResponse
    {
        return ProductService::getProductVariantBySlug($slug);
    }

    public function productSuggestions(Request $request)
    {
        $products = ProductService::getProductSuggestions($request);

        return success('Product suggestions fetched successfully', $products);
    }

    public function productKeywordSuggestions(Request $request)
    {
        $products = ProductService::getProductKeywordSuggestions($request);

        return success('Product keyword suggestions fetched successfully', $products);
    }

    public function shopProducts(Request $request): JsonResponse
    {
        try {
            $products = $this->productService->getNewShopProducts($request);
            $isCursor = request()->get('is-cursor') ?? false;

            if ($isCursor) {
                $data = response()->json(
                    [
                        'message' => 'Shop products fetched successfully',
                        'data' => ProductsResource::collection(resource: $products->items()),
                        'total' => $products->total_count ?? null,
                        'last_page' => null,
                        'current_page' => request('cursor') ?? null,
                        'next_page_url' => $products->nextCursor()?->encode(),
                        'prev_page_url' => $products->previousCursor()?->encode(),
                        'is_random' => $products->is_random ?? false,
                        'flags' => [
                            'is_random' => $products->is_random ?? false,
                        ],
                    ],
                    200,
                );
            } else {
                $data = response()->json(
                    [
                        'message' => 'Shop products fetched successfully',
                        'data' => ProductsResource::collection(resource: $products->items()),
                        'total' => $products->total(),
                        'last_page' => $products->lastPage(),
                        'current_page' => $products->currentPage(),
                        'next_page_url' => $products->nextPageUrl(),
                        'is_random' => $products->is_random ?? false,
                        'flags' => [
                            'is_random' => $products->is_random ?? false,
                        ],
                    ],
                    200,
                );
            }

            //cache forget delivery_charges_settings
            Cache::forget('delivery_charges_settings');

            return $data;
        } catch (\Throwable $th) {
            return failure('Something went wrong', 500);
        }
    }

    public function shopProductDetails($slug): JsonResponse
    {
        $product = $this->productService->getNewShopProductDetails($slug);

        if (!$product) {
            return failure('Product not found', 404);
        }

        return success('Product details fetched successfully', new ProductDetailsResource($product));
    }

    public function newArrivals(Request $request): JsonResponse
    {
        $products = $this->productService->getNewArrivals($request);

        return formatPagination('Show all new arrival products', $products);
    }

    public function bestSellings(Request $request): JsonResponse
    {
        $products = $this->productService->getBestSellings($request);

        return formatPagination('Show all best selling products', $products);
    }

    public function shopForMe(Request $request)
    {
        try {
            $products = $this->productService->getShopForMe($request);
            $isCursor = request()->get('is-cursor') ?? false;

            if ($isCursor) {
                return response()->json(
                    [
                        'message' => 'Shop for me products fetched successfully',
                        'data' => ProductsResource::collection($products->items()),
                        'total' => $products->total_count ?? null,
                        'last_page' => null,
                        'current_page' => request('cursor') ?? null,
                        'next_page_url' => $products->nextCursor()?->encode(),
                        'prev_page_url' => $products->previousCursor()?->encode(),
                    ],
                    200,
                );
            }else{
                return response()->json([
                    'message'       => 'Shop for me products fetched successfully',
                    'data'          => ProductsResource::collection($products->items()),
                    'total'         => $products->total(),
                    'last_page'     => $products->lastPage(),
                    'current_page'  => $products->currentPage(),
                    'next_page_url' => $products->nextPageUrl(),
                ], 200);
            }
            
        } catch (\Throwable $th) {
            return failure('Something went wrong', 500);
        }
    }

    public function forYou(Request $request, $merchant_id): JsonResponse
    {
        try {
            $products = $this->productService->getForYouProducts($merchant_id, $request);
            $isCursor = request()->get('is-cursor') ?? false;
            
            if ($isCursor) {
                return response()->json(
                    [
                        'message' => 'For you products fetched successfully',
                        'data' => ProductsResource::collection($products->items()),
                        'total' => $products->total_count ?? 0,
                        'last_page' => null,
                        'current_page' => request('cursor') ?? null,
                        'next_page_url' => $products->nextCursor()?->encode(),
                        'prev_page_url' => $products->previousCursor()?->encode(),
                    ],
                    200,
                );
            }else{
                return response()->json([
                    'message'       => 'For you products fetched successfully',
                    'data'          => ProductsResource::collection($products->items()),
                    'total'         => $products->total(),
                    'last_page'     => $products->lastPage(),
                    'current_page'  => $products->currentPage(),
                    'next_page_url' => $products->nextPageUrl(),
                ], 200);
            }
        } catch (ModelNotFoundException $e) {
            return failure('Merchant not found', 404);
        } catch (\Throwable $th) {
            return failure('Something went wrong', 500);
        }
    }
}
