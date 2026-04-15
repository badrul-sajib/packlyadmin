<?php

use Modules\Api\V1\Ecommerce\Review\Http\Controllers\ReviewController;


Route::middleware(['api.check', 'auth:sanctum'])->group(function () {
    Route::controller(ReviewController::class)->group(function () {
        Route::post('/reviews', 'store');
        Route::get('/my/reviews', 'myReviews');
        Route::put('/review/{review}', 'update');
        Route::get('/to/reviews', 'toReviews');
        Route::get('/shop/{id}/reviews', 'shopReviews');
        Route::get('/reviews/{review}', 'show');
    });
});

Route::middleware(['api.check'])->group(function () {
    Route::get('/product/{slug}/reviews', [ReviewController::class, 'ProductReviews']);
});