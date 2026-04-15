<?php

use Modules\Api\V1\Ecommerce\Auth\Http\Controllers\AuthController;
use Modules\Api\V1\Ecommerce\Auth\Http\Controllers\SocialiteController;


Route::middleware(['api.check', 'hit.control'])->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::post('/send-otp', 'sendOtp')->middleware('json.throttle:6,1');
        Route::post('/login', 'login');
        Route::post('/verify-otp', 'verifyOtp');
        Route::post('/register', 'register');
        Route::post('/reset-password', 'resetPassword');
        Route::post('/reset-confirm', 'resetConfirm');
    });

    Route::controller(SocialiteController::class)->group(function () {
        Route::get('/auth/{provider}/redirect', 'redirect');
        Route::get('/auth/{provider}/callback', 'callback');
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::controller(AuthController::class)->group(function () {
            Route::post('/send-otp-email', 'sendOtpToEmail');
            Route::post('/verify-otp-email', 'verifyOtpEmail');
            Route::get('/profile', 'getUserDetails');
            Route::post('/profile/update', 'updateUser');
            Route::post('/new-password', 'newPassword');
            Route::post('/logout', 'logout');
            Route::post('/change-password', 'changePassword');
        });
    });
});