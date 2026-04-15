<?php

use Modules\Api\V1\Ecommerce\Category\Http\Controllers\CategoryController;


Route::middleware('api.check')->group(function () {
    Route::controller(CategoryController::class)->group(function () {
        Route::get('/categories', 'index');
    });
});