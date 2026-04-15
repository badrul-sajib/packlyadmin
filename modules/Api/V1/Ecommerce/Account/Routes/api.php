<?php

use Modules\Api\V1\Ecommerce\Account\Http\Controllers\AccountDeleteRequestController;


Route::middleware(['api.check', 'auth:sanctum'])->group(function () {
    Route::controller(AccountDeleteRequestController::class)->group(function () {
        Route::post('/account/delete-request', 'store');
        Route::get('/account/delete-request/status', 'checkPendingRequest');
    });
});