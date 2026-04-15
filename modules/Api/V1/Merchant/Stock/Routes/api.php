<?php

use Modules\Api\V1\Merchant\Stock\Http\Controllers\StockController;
use Modules\Api\V1\Merchant\Stock\Http\Controllers\StockTransferController;


Route::middleware(['auth:sanctum', 'token.expiration', 'hit.control', 'role.check', 'business.manager.api.check'])->group(function () {
    Route::get('/stock-transfer', [StockTransferController::class, 'index']);
    Route::post('/stock-transfer', [StockTransferController::class, 'store']);
    Route::get('/stock-transfer/{id}', [StockTransferController::class, 'show']);

    // Additional stock routes
    Route::get('/warehouse-products', [StockTransferController::class, 'search']);
    Route::get('/stock-summary', [StockController::class, 'index']);
});
