<?php

namespace Modules\Api\V1\Ecommerce\Category\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        return CategoryService::getCategories();
    }
}
