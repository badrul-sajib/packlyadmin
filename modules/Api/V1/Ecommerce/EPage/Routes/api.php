<?php

use Modules\Api\V1\Ecommerce\EPage\Http\Controllers\EPageController;


Route::middleware('api.check')->group(function () {
    Route::controller(EPageController::class)->group(function () {
        Route::get('/e-pages', 'index');
        Route::get('/e-pages/{slug}', 'show');
    });
});