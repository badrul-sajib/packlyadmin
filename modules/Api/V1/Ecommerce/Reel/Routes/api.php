<?php

use Modules\Api\V1\Ecommerce\Reel\Http\Controllers\ReelController;


Route::middleware(['api.check', 'auth:sanctum'])->group(function () {
    Route::post('shops/reels/userAction', [ReelController::class, 'userAction']);
});