<?php

use Modules\Api\V1\Merchant\Return\Http\Controllers\ReturnController;


Route::middleware(['auth:sanctum', 'token.expiration', 'hit.control', 'business.manager.api.check'])->group(function () {
    Route::controller(ReturnController::class)->group(function () {
        Route::get('/returns', 'index');
        Route::get('/returns/{id}', 'show');
        Route::patch('/returns/{id}', 'update');
        Route::get('/steadfast-return-status/{returnId}', 'steadfastReturnStatus');
    });
});
