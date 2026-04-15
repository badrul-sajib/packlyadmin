<?php

namespace Modules\Api\V1\Merchant\Category\Http\Controllers;

use App\Services\ApiResponse;
use App\Models\Category\Category;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Models\Category\SubCategory;
use App\Models\Category\SubCategoryChild;
use App\Models\Product\Product;
use App\Services\Merchant\MerchantCategoryService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends Controller
{

    public function __construct()
    {
        $this->middleware('shop.permission:show-category')->only('index');
    }
    /*
     * Lists all product categories.
     */
    public function index(Request $request): JsonResponse
    {
        $type = $request->query('type');
        $categoryQuery = Category::select('id', 'name', 'status')->orderBy('id');
        if ($type == "merchant") {
            $merchantId = auth()->user()->merchant->id;

            $categoryQuery->whereIn('id', function ($q) use ($merchantId) {
                $q->select('category_id')
                    ->from('products')
                    ->where('merchant_id', $merchantId)
                    ->whereNull('deleted_at')
                    ->distinct();
            });
        }

        $categories = $categoryQuery->get();


        return ApiResponse::success('Categories retrieved successfully', $categories, Response::HTTP_OK);
    }


    public function categories()
    {
        try {
            // category type 1 for main, 2 for sub with child
            $category_type = request('category_type', 1);
            $parent_id     = request('parent_id', null);
            $data = null;

            if ($category_type == 1) {
                $data =  $this->getCategories();
            }
            if ($category_type == 2) {
                $data =  $this->getSubCategoriesWithChild($parent_id);
            }



            return ApiResponse::success('Categories retrieved successfully', $data, Response::HTTP_OK);
        } catch (\Throwable $th) {
            return ApiResponse::failure('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function categorySearch()
    {
        try {
            $search = request('search');

            if (!$search) {
                return ApiResponse::failure('Search keyword is required', Response::HTTP_UNPROCESSABLE_ENTITY);
            }


            $categories = Category::select('id', 'name')
                ->where('name', 'LIKE', "%{$search}%")
                ->orderBy('name')
                ->get();

            $subCategories = SubCategory::select('id', 'name', 'category_id')
                ->with(['category:id,name'])
                ->where('name', 'LIKE', "%{$search}%")
                ->orderBy('name')
                ->get()->map(function ($item) {
                    return [
                        'id'        => $item->id,
                        'name'      => $item->name,
                        'category'  => $item->category
                    ];
                });

            $childCategories = SubCategoryChild::select('id', 'name', 'sub_category_id')
                ->with(['subCategory:id,name,category_id', 'subCategory.category:id,name'])
                ->where('name', 'LIKE', "%{$search}%")
                ->orderBy('name')
                ->get()->map(function ($item) {
                    return [
                        'id'            => $item->id,
                        'name'          => $item->name,
                        'category'      => $item->subCategory->category,
                        'subCategory'   => [
                            'id'    => $item->subCategory->id,
                            'name'  => $item->subCategory->name
                        ]
                    ];
                });


            return ApiResponse::success(
                'Search results fetched successfully',
                [
                    'categories'       => $categories,
                    'sub_categories'   => $subCategories,
                    'child_categories' => $childCategories,
                    'meta' => [
                        'keyword' => $search,
                        'total_matches' =>
                        $categories->count()
                            + $subCategories->count()
                            + $childCategories->count(),
                    ],
                ]
            );
        } catch (\Throwable $th) {
            return ApiResponse::failure('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    protected function getCategories()
    {
        return Category::select('id', 'name')->orderBy('id')->get();
    }

    protected function getSubCategoriesWithChild($parent_id)
    {
        return SubCategory::where('category_id', $parent_id)
            ->with(['category:id,name', 'subchilds:id,name,sub_category_id'])
            ->select('id', 'name', 'category_id')
            ->orderBy('id')->get()->map(function ($item) {
                return [
                    'id'                => $item->id,
                    'name'              => $item->name,
                    'category_id'       => $item->category_id,
                    'category'          => $item->category,
                    'child_categories'  => $item->subchilds
                ];
            });
    }

    public function getAllCategoryWithChild(): JsonResponse
    {
        return MerchantCategoryService::getAllCategoryWithChild();
    }
}
