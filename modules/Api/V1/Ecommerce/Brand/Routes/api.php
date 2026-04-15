<?php

use Modules\Api\V1\Ecommerce\Brand\Http\Controllers\BrandController;


Route::middleware('api.check')->group(function () {
    Route::controller(BrandController::class)->group(function () {
        Route::get('/brands', 'index');
    });
});