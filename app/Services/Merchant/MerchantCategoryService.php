<?php

namespace App\Services\Merchant;

use App\Exceptions\CategoryCreationException;
use App\Models\Category\Category;
use App\Models\Category\SubCategory;
use App\Models\Category\SubCategoryChild;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class MerchantCategoryService
{
    const MAIN = '1';

    const SUB = '2';

    const CHILD = '3';

    public static function getAllCategoryWithChild(): JsonResponse
    {
        try {
            $categories =

                Category::active()
                ->with([

                    'subcategories' => function ($q) {
                        $q->orderBy('name');
                    },
                ])
                ->orderBy('name')
                ->get()
                ->map(function ($category) {
                    return [
                        'id'            => $category->id,
                        'name'          => $category->name,

                        'subcategories' => $category->subcategories->map(function ($subcategory) {
                            return [
                                'id'        => $subcategory->id,
                                'name'      => $subcategory->name,

                                'subchilds' => $subcategory->subchilds->map(function ($subchild) {
                                    return [
                                        'id'    => $subchild->id,
                                        'name'  => $subchild->name,
                                    ];
                                })->values(),
                            ];
                        })->values(),
                    ];
                })->values();


            return success('Categories retrieved successfully', $categories);
        } catch (Exception $e) {
            Log::error('getCategories error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return failure('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
