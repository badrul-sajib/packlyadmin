<?php

use Modules\Api\V1\Ecommerce\Slider\Http\Controllers\SliderController;


Route::middleware('api.check')->group(function () {
    Route::controller(SliderController::class)->group(function () {
        Route::get('/sliders', 'index');
        Route::get('/slider/promotions', 'promotionProducts');
    });
});