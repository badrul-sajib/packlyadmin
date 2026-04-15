<?php

use Modules\Api\V1\Merchant\Setting\Http\Controllers\ShopSettingsController;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth:sanctum', 'token.expiration', 'hit.control', 'business.manager.api.check'])->group(function () {
    Route::controller(ShopSettingsController::class)->group(function () {
        Route::post('/shop-name', 'updateShopName');
        Route::get('/shop-settings', action: 'index');
        Route::get('/shop-info', 'show');
        Route::get('/shop-products', 'shopProducts');
        Route::post('/shop-settings', 'update');
        Route::post('/shop-update-request', 'updateShopRequest');
        Route::post('/shop-status-seen', 'updateShopStatusSeen');
        Route::post('/shop-address/update', 'updateShopAddress');
    });
});
