<?php

use Modules\Api\V1\Merchant\CategoryRequest\Http\Controllers\CategoryRequestController;

Route::middleware(['auth:sanctum', 'token.expiration', 'hit.control', 'business.manager.api.check'])->group(function () {
    Route::get('/category-request-list', [CategoryRequestController::class, 'index']);
    Route::post('/category-create-request', [CategoryRequestController::class, 'store']);
});
