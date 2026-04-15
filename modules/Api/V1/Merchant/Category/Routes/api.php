<?php

use Modules\Api\V1\Merchant\Category\Http\Controllers\CategoryController;
use Modules\Api\V1\Merchant\Category\Http\Controllers\SubCategoryChildController;
use Modules\Api\V1\Merchant\Category\Http\Controllers\SubCategoryController;


Route::middleware(['auth:sanctum', 'token.expiration', 'hit.control', 'business.manager.api.check'])->group(function () {
    // Category
    Route::get('/all-categories', [CategoryController::class, 'index']);
    Route::get('/get-all-categories-with-all-child', [CategoryController::class, 'getAllCategoryWithChild']);
    // Sub Category
    Route::get('/sub-categories-by-category/{id}', [SubCategoryController::class, 'subCategoriesByCategory']);
    // Sub Category Child
    Route::get('/subcat-child-by-subcategory/{id}', [SubCategoryChildController::class, 'subcategoryChildBySubcategory']);


    Route::get('/categories', [CategoryController::class, 'categories']);
    Route::get('/categories-search', [CategoryController::class, 'categorySearch']);
});
