<?php

use Modules\Api\V1\Merchant\Notice\Http\Controllers\NoticeController;

Route::middleware(['auth:sanctum', 'token.expiration', 'hit.control', 'business.manager.api.check'])->group(function () {
    Route::controller(NoticeController::class)
        ->prefix('merchants')
        ->group(function () {
            Route::get('/reports', 'index');
            Route::get('/report/{id}', 'show');
            Route::post('/report/{id}', 'update');
        });
});
