<?php

use Modules\Api\V1\Merchant\Profile\Http\Controllers\ProfileController;


Route::middleware(['auth:sanctum', 'token.expiration', 'hit.control', 'role.check', 'business.manager.api.check'])->group(function () {
    Route::controller(ProfileController::class)
        ->group(function () {
            Route::get('/profile', 'show');
            Route::post('/profile', 'update');
            Route::put('/password/reset', 'passwordReset');
        });
});
