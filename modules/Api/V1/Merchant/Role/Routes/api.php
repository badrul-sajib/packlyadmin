<?php

use Illuminate\Support\Facades\Route;
use Modules\Api\V1\Merchant\Role\Http\Controllers\RoleController;


Route::prefix('api/v1/merchant')
    ->middleware(['auth:sanctum', 'token.expiration', 'hit.control', 'role.check', 'business.manager.api.check'])
    ->name('api.merchant/role.')->group(function () {
        Route::resource('roles', RoleController::class);
        Route::get('permissions', [RoleController::class, 'permissions'])->name('roles.permissions');
        Route::patch('roles/{id}/status', [RoleController::class, 'updateStatus'])->name('roles.updateStatus');
    });
