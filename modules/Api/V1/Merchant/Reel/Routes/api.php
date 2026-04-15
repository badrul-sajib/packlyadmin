<?php

use Modules\Api\V1\Merchant\Reel\Http\Controllers\ReelController;


Route::middleware(['auth:sanctum', 'token.expiration', 'hit.control', 'role.check', 'business.manager.api.check'])->group(function () {
    Route::apiResource('/reels', ReelController::class);
});
