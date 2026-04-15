<?php

use Modules\Api\V1\Ecommerce\Promotion\Http\Controllers\PromotionServiceController;


Route::middleware('api.check')->group(function () {
    Route::controller(PromotionServiceController::class)->group(function () {
        Route::get('/promotion-service', 'index');
    });
});