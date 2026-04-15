<?php

use Modules\Api\V1\Merchant\Report\Http\Controllers\ReportController;


Route::middleware(['auth:sanctum', 'token.expiration', 'hit.control', 'role.check', 'business.manager.api.check'])->group(function () {
    Route::get('/reports', [ReportController::class, 'index']);
    Route::get('/reports/balance-sheet', [ReportController::class, 'showBalanceSheet']);
});
