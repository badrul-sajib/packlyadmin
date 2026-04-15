<?php

use Modules\Api\V1\Merchant\Issue\Http\Controllers\IssueController;
use Modules\Api\V1\Merchant\Issue\Http\Controllers\IssueTypeController;


Route::middleware(['auth:sanctum', 'token.expiration', 'hit.control', 'role.check', 'business.manager.api.check'])->group(function () {
    Route::get('/issue-types', [IssueTypeController::class, 'index']);

    Route::get('/issues', [IssueController::class, 'index']);
    Route::post('/issues', [IssueController::class, 'store']);
});
