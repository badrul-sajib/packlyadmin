<?php

use Modules\Api\V1\Ecommerce\Checkout\Http\Controllers\CheckoutController;
use Modules\Api\V1\Ecommerce\Checkout\Http\Controllers\InsideDhakaController;


Route::middleware(['api.check', 'auth:sanctum'])->group(function () {
    Route::controller(CheckoutController::class)->group(function () {
        Route::post('/checkout', 'checkout');
    });
});

Route::middleware(['api.check'])->group(function () {
    Route::post('check-inside-dhaka', [InsideDhakaController::class, 'check']);
});