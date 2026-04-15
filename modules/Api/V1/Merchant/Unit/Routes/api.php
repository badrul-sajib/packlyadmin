<?php

use Modules\Api\V1\Merchant\Unit\Http\Controllers\UnitController;


Route::middleware(['auth:sanctum', 'token.expiration', 'hit.control', 'role.check', 'business.manager.api.check'])->group(function () {
    Route::apiResource('units', UnitController::class);
    Route::patch('/change-unit-status/{id}', [UnitController::class, 'status']);
});
