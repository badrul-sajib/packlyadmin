<?php

use Modules\Api\V1\Merchant\Auth\Http\Controllers\AuthController;
use Modules\Api\V1\Merchant\Auth\Http\Controllers\MerchantController;

Route::middleware(['business.manager.api.check'])->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('/send-otp', [AuthController::class, 'sendOtp'])->name('sendOtp');
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('/register-verify-otp', [AuthController::class, 'registerVerifyOtp']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    Route::post('/register-with-key', [AuthController::class, 'registerWithKey']);
    Route::post('/merchant-register', [MerchantController::class, 'store']);
});


Route::middleware(['auth:sanctum', 'token.expiration', 'hit.control', 'business.manager.api.check'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('/disable-merchant', [AuthController::class, 'disableMerchant']);
});
