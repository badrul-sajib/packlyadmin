<?php

use Modules\Api\V1\Ecommerce\Location\Http\Controllers\LocationController;


Route::middleware(['api.check', 'auth:sanctum'])->group(function () {
    Route::controller(LocationController::class)->group(function () {
        Route::get('/location', 'location');
    });
});