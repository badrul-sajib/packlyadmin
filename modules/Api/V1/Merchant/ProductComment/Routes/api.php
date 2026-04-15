<?php

use Modules\Api\V1\Merchant\ProductComment\Http\Controllers\ProductCommentController;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth:sanctum', 'token.expiration', 'hit.control', 'role.check', 'business.manager.api.check'])->group(function () {
    // Product comment routes
    Route::get('/product-comments', [ProductCommentController::class, 'index']);
    Route::delete('/product-comments/{id}', [ProductCommentController::class, 'delete']);
    Route::post('/product-comments/{id}/reply', [ProductCommentController::class, 'reply']);
});
