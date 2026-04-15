<?php

use Modules\Api\V1\Ecommerce\ShopSetting\Http\Controllers\ShopSettingController;

Route::middleware('api.check')->group(function () {
    Route::controller(ShopSettingController::class)->group(function () {
        Route::get('/shop/settings', 'index');
        Route::get('/analytics-tags', 'getAnalyticsTags');
    });
});
