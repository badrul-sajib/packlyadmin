<?php

use Modules\Api\V1\Ecommerce\Reason\Http\Controllers\ReasonController;


Route::middleware(['api.check', 'auth:sanctum'])->group(function () {
    Route::controller(ReasonController::class)->group(function () {
        Route::get('/reasons', 'reasons');
    });
});