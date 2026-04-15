<?php

use Modules\Api\V1\Merchant\Product\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth:sanctum', 'token.expiration', 'hit.control', 'role.check', 'business.manager.api.check'])->group(function () {
    Route::apiResource('products', ProductController::class)->parameters(['products' => 'product:slug']);
    Route::get('product/trash-list', [ProductController::class, 'trashList']);
    Route::patch('product/{product}/restore', [ProductController::class, 'restore']);
    Route::delete('product/{product}/permanent-delete', [ProductController::class, 'hideFromTrash']);

    Route::get('/search-product', [ProductController::class, 'search']);
    Route::get('/product-variations/{slug}', [ProductController::class, 'variations']);
    Route::put('/product-variations/{slug}', [ProductController::class, 'updateVariations']);
    Route::put('/product-status-change/{slug}', [ProductController::class, 'productStatusChange']);
    Route::post('/product-import', [ProductController::class, 'import']);
    Route::post('/product-import/validate', [ProductController::class, 'validateImport']);
});
