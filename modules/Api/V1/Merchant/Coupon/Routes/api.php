<?php

use Modules\Api\V1\Merchant\Coupon\Http\Controllers\CouponController;


Route::middleware(['auth:sanctum', 'token.expiration', 'hit.control', 'business.manager.api.check'])->group(function () {
    Route::apiResource('coupons', CouponController::class);
});
