<?php

use Modules\Api\V1\Merchant\Attribute\Http\Controllers\AttributeController;


Route::middleware(['auth:sanctum', 'token.expiration', 'hit.control', 'role.check', 'business.manager.api.check'])->group(function () {
    Route::post('/add-attribute', [AttributeController::class, 'store']);
    Route::put('/update-attribute/{slug}', [AttributeController::class, 'update']);
    Route::get('/attribute-list', [AttributeController::class, 'index']);
    Route::get('/attribute-list-show/{slug}', [AttributeController::class, 'show']);
    Route::delete('/delete-attribute/{slug}', [AttributeController::class, 'destroy']);
});
