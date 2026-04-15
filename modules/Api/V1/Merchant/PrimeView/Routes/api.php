<?php

use Modules\Api\V1\Merchant\PrimeView\Http\Controllers\PrimeViewController;
use Modules\Api\V1\Merchant\PrimeView\Http\Controllers\PrimeViewProductController;


Route::middleware(['auth:sanctum', 'token.expiration', 'hit.control', 'role.check', 'business.manager.api.check'])->group(function () {
    Route::apiResource('/prime-view-products', PrimeViewProductController::class);
    Route::get('/prime-view-list', [PrimeViewController::class, 'index']);
});
