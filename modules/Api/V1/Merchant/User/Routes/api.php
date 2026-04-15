<?php

use Illuminate\Support\Facades\Route;
use Modules\Api\V1\Merchant\User\Http\Controllers\UserController;

Route::prefix('api/v1/merchant')
    ->middleware(['auth:sanctum', 'token.expiration', 'hit.control', 'role.check', 'business.manager.api.check'])
    ->name('api.merchant/user.')->group(function () {
        Route::resource('users', UserController::class);
        Route::put('users/{user}/status', [UserController::class, 'updateStatus']);
    });
