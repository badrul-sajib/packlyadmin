<?php

namespace Modules\Api\V1\Merchant\Category\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category\SubCategory;
use App\Services\ApiResponse;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class SubCategoryController extends Controller
{

    public function __construct()
    {
        $this->middleware('shop.permission:show-subcategory')->only('index');
    }
    /*
     * Fetches subcategories for a category.
     */
    public function subCategoriesByCategory(int $id): JsonResponse
    {
        try {
            $subCategories = SubCategory::with('category')->where('category_id', $id)->get();

            return ApiResponse::success('Sub categories by category deleted successfully', $subCategories, Response::HTTP_OK);
        } catch (\Exception $e) {
            return ApiResponse::failure('Sub categories not found.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
