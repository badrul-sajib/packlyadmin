<?php

use Illuminate\Support\Facades\Route;
use Modules\Api\V1\Ecommerce\Unit\Http\Controllers\UnitController;

Route::prefix('api/v1/ecommerce/unit')
    ->name('api.ecommerce/unit.')->group(function () {
        Route::get('/all-units', [UnitController::class, 'index']);
    });
