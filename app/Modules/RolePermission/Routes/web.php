<?php

use App\Modules\RolePermission\Controllers\RoleController;
use App\Modules\RolePermission\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:admin'])->group(function () {

    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);
    Route::patch('/users/reset-password/{id}', [UserController::class, 'resetPassword'])->name('users.reset-password');
    Route::patch('/users/{id}/change-role', [UserController::class, 'changeRole'])->name('users.change-role');
});
