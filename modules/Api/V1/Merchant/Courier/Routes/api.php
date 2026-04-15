<?php

use Modules\Api\V1\Merchant\Courier\Http\Controllers\CourierController;


Route::middleware(['auth:sanctum', 'token.expiration', 'hit.control', 'role.check', 'business.manager.api.check'])->group(function () {
    Route::get('/couriers', [CourierController::class, 'index']);
});
