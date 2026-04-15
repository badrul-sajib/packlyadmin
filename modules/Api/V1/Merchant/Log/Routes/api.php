<?php

use App\Http\Controllers\ErrorLogController;
use Illuminate\Support\Facades\Route;


Route::middleware(['api.check', 'business.manager.api.check'])->group(function () {
    Route::post('/log-error', [ErrorLogController::class, 'store']);
});
