<?php

use Illuminate\Support\Facades\Route;
use Modules\Api\V1\Ecommerce\Attribute\Http\Controllers\AttributeController;

Route::prefix('api/v1/ecommerce/attribute')
    ->name('api.ecommerce/attribute.')->group(function () {
        Route::get('/', [AttributeController::class, 'index']);
    });
