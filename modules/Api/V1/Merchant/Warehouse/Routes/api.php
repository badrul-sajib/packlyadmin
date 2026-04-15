<?php

use Modules\Api\V1\Merchant\Warehouse\Http\Controllers\WarehouseController;


Route::middleware(['auth:sanctum', 'token.expiration', 'hit.control', 'role.check', 'business.manager.api.check'])->group(function () {
    Route::apiResource('warehouses', WarehouseController::class)->except(['create', 'edit']);

    // Additional warehouse routes
    Route::patch('/change-warehouse-status/{id}', [WarehouseController::class, 'status']);
    Route::get('/warehouse-report/{id}', [WarehouseController::class, 'report']);
});
