<?php

use Modules\Api\V1\Ecommerce\Cart\Http\Controllers\CartController;


Route::middleware(['api.check', 'auth:sanctum'])->group(function () {
    Route::controller(CartController::class)->group(function () {
        Route::get('cart', 'index');
        Route::post('cart', 'store');
        Route::delete('/cart', 'destroyItems');
    });
});