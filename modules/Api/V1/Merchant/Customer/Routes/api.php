<?php

use Modules\Api\V1\Merchant\Customer\Http\Controllers\CustomerController;


Route::middleware(['auth:sanctum', 'token.expiration', 'hit.control', 'role.check', 'business.manager.api.check'])->group(function () {
    Route::apiResource('customers', CustomerController::class);
});
