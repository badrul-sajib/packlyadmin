<?php

use Modules\Api\V1\Merchant\EProduct\Http\Controllers\EProductController;


Route::middleware(['auth:sanctum', 'token.expiration', 'hit.control', 'role.check', 'business.manager.api.check'])->group(function () {
    Route::get('/e-products', [EProductController::class, 'index']);
    Route::get('/shop-e-products', [EProductController::class, 'shopProducts']);
    Route::get('/shop-e-products/{id}', [EProductController::class, 'shopProductDetails']);
    Route::post('/e-products', [EProductController::class, 'store']);

    // Additional e-product routes
    Route::get('/search-e-product', [EProductController::class, 'search']);
    Route::patch('/change-e-product-status/{id}', [EProductController::class, 'status']);
    Route::put('/bulk-status-e-product', [EProductController::class, 'bulkStatus']);
});
