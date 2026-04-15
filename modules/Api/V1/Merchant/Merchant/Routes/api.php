<?php

use Modules\Api\V1\Merchant\Merchant\Http\Controllers\MerchantVerificationController;


Route::middleware(['auth:sanctum', 'token.expiration', 'hit.control', 'business.manager.api.check'])->group(function () {
    Route::controller(MerchantVerificationController::class)->group(function () {
        Route::post('/verification/submit', 'submit');
    });
});
