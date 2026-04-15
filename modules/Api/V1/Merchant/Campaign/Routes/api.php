<?php

use Illuminate\Support\Facades\Route;
use Modules\Api\V1\Merchant\Campaign\Http\Controllers\CampaignController;

Route::middleware(['auth:sanctum', 'token.expiration', 'hit.control', 'business.manager.api.check'])->controller(CampaignController::class)->prefix('api/v1/merchant/campaigns')->group(function () {
    Route::get('/', 'index');
    Route::get('details/{campaign}', 'show');
    Route::get('search-product', 'search');
    Route::post('request/products', 'requestProducts');
    Route::get('request/products', 'getRequestProducts');
    Route::get('register', 'registerCampaigns');
});
