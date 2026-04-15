<?php

namespace Modules\Api\V1\Merchant\Category\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category\SubCategoryChild;
use App\Services\ApiResponse;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class SubCategoryChildController extends Controller
{
    public function __construct()
    {
        $this->middleware('shop.permission:show-subcategory-child')->only('index');
    }
    /*
     * Lists child subcategories of a subcategory.
     */
    public function subcategoryChildBySubcategory(int $id): JsonResponse
    {
        try {
            $subCategoryChildren = SubCategoryChild::with('subCategory')->where('sub_category_id', $id)->get();

            return ApiResponse::success('Sub category children by sub category', $subCategoryChildren);
        } catch (\Exception $e) {
            return ApiResponse::failure('Sub category children not found.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
