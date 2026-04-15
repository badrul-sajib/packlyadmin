<?php

use Modules\Api\V1\Ecommerce\Coupon\Http\Controllers\CouponController;

Route::middleware(['api.check', 'auth:sanctum'])->group(function () {
    Route::controller(CouponController::class)->group(function () {
        Route::get('/coupons/{product_id}', 'coupons');
        Route::post('/coupon-product-eligibility', 'couponProductEligibility');
        Route::post('/shop-coupon-product-eligibility', 'shopCouponProductEligibility');
    });
});

Route::get('/coupon-default', [CouponController::class, 'couponDefault']);
