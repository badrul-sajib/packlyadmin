<?php

use Modules\Api\V1\Ecommerce\Wishlist\Http\Controllers\WishlistController;


Route::middleware(['api.check', 'auth:sanctum'])->group(function () {
    Route::controller(WishlistController::class)->group(function () {
        Route::post('/wishlist/toggle/{product_id}', 'toggle');
        Route::get('/wishlist', 'index');
    });
});