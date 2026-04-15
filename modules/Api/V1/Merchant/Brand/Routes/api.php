<?php

use Modules\Api\V1\Merchant\Brand\Http\Controllers\BrandController;


Route::middleware(['auth:sanctum', 'token.expiration', 'hit.control', 'business.manager.api.check'])->group(function () {
    Route::get('/brands', [BrandController::class, 'index']);
    Route::patch('/change-status/{id}', [BrandController::class, 'status']);
});
