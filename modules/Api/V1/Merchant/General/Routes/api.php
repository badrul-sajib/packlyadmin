<?php

use Modules\Api\V1\Merchant\General\Http\Controllers\SellWithUsController;
use Modules\Api\V1\Merchant\General\Http\Controllers\WebhookController;


Route::middleware(['auth:sanctum', 'token.expiration', 'hit.control', 'business.manager.api.check'])->group(function () {
    Route::post('{uuid}/callback', [WebhookController::class, 'webhook']);
});

Route::get('/sell-with-us', [SellWithUsController::class, 'index']);
