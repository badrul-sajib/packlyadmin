<?php

use Modules\Api\V1\Merchant\Message\Http\Controllers\MessageController;


Route::middleware(['auth:sanctum', 'token.expiration', 'hit.control', 'role.check', 'business.manager.api.check'])->group(function () {
    Route::post('/send-message', [MessageController::class, 'store']);
    Route::get('/send-message', [MessageController::class, 'index']);
});
