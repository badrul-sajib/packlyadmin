<?php

use Modules\Api\V1\Merchant\Type\Http\Controllers\AccountTypeController;


Route::middleware(['auth:sanctum', 'token.expiration', 'hit.control', 'role.check', 'business.manager.api.check'])->group(function () {
    Route::get('/account-types', [AccountTypeController::class, 'index']);
});
