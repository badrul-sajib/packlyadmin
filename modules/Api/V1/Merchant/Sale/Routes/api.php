<?php

use Modules\Api\V1\Merchant\Sale\Http\Controllers\SellProductController;


Route::middleware(['auth:sanctum', 'token.expiration', 'hit.control', 'role.check', 'business.manager.api.check'])->group(function () {
    Route::get('/sell-products', [SellProductController::class, 'index']);
    Route::post('/sell-products', [SellProductController::class, 'store']);
    Route::get('/sell-products/{id}', [SellProductController::class, 'show']);

    // Additional sell product routes
    Route::post('/sell-product-shipping-fee-manage', [SellProductController::class, 'manageShippingFee']);
    Route::patch('/sell-product-status-change/{id}', [SellProductController::class, 'sellStatusChange']);
    Route::put('/sell-product-update/{id}', [SellProductController::class, 'sellUpdate']);
});
