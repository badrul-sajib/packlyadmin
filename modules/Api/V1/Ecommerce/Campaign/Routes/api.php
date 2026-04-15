<?php

use Illuminate\Support\Facades\Route;
use Modules\Api\V1\Ecommerce\Campaign\Http\Controllers\CampaignController;
use Modules\Api\V1\Ecommerce\Campaign\Http\Controllers\GiveawayController;

Route::prefix('api/v1/ecommerce/campaigns')->group(function () {
    Route::get('/{slug}', [CampaignController::class, 'show']);
    Route::get('/prime-view/{slug}', [CampaignController::class, 'primeView']);
});

Route::middleware(['api.check', 'auth:sanctum'])->prefix('api/v1/ecommerce/giveaway')->group(function () {
    Route::get('/tickets', [GiveawayController::class, 'tickets']);
});
