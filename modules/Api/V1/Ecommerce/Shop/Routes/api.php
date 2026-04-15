<?php

use Illuminate\Support\Facades\Route;
use Modules\Api\V1\Ecommerce\Shop\Http\Controllers\ShopController;

Route::middleware('api.check')->group(function () {
    Route::controller(ShopController::class)->group(function () {
        Route::get('/shop/{id}/details', 'basicDetails');
        Route::get('/shop/{id}/products', 'shopProducts');
        Route::get('/shops/popular', 'popularShops');
        Route::get('/shops/reels', 'shopReels');
        Route::get('/shop-coupon/{slug}', 'shopCoupon');
        Route::get('/shops', 'allShops');
        Route::get('/shops/nearby', 'nearbyShops');
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::controller(ShopController::class)->group(function () {
            Route::post('/shop/{id}/follow', 'follow');
        });
    });
});
