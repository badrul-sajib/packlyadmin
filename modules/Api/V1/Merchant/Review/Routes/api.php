<?php

use Modules\Api\V1\Merchant\Review\Http\Controllers\ReviewController;


Route::middleware(['auth:sanctum', 'token.expiration', 'hit.control', 'role.check', 'business.manager.api.check'])->group(function () {
    Route::get('/reviews', [ReviewController::class, 'index']);
    Route::post('/reviews/{id}/reply', [ReviewController::class, 'reply']);
});
