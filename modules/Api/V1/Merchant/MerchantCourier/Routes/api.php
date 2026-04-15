<?php

use Modules\Api\V1\Merchant\MerchantCourier\Http\Controllers\MerchantCourierController;


Route::middleware(['auth:sanctum', 'token.expiration', 'hit.control', 'role.check', 'business.manager.api.check'])->group(function () {
    Route::get('/merchant-courier', [MerchantCourierController::class, 'index']);
    Route::post('/merchant-courier', [MerchantCourierController::class, 'store']);
    Route::put('/merchant-courier/{id}', [MerchantCourierController::class, 'update']);
    Route::delete('/merchant-courier/{id}', [MerchantCourierController::class, 'detachCourier']);
});
