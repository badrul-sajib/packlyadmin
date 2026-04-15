<?php

use Modules\Api\V1\Ecommerce\Product\Http\Controllers\ProductCommentController;
use Modules\Api\V1\Ecommerce\Product\Http\Controllers\ProductController;
use Modules\Api\V1\Ecommerce\Product\Http\Controllers\PublicProductController;
use Modules\Api\V1\Ecommerce\Review\Http\Controllers\PublicReviewController;
use Modules\Api\V1\Ecommerce\Product\Http\Controllers\PublicEventController;


Route::middleware('api.check')->group(function () {
    Route::controller(ProductController::class)->group(function () {
        Route::get('products/new-arrival', 'newArrivals');
        Route::get('products/best-selling', 'bestSellings');
        Route::get('/product/{slug}', 'productDetails');
        Route::get('/product/{slug}/variant', 'productVariant');
        Route::get('/products/suggestions', 'productSuggestions');
        Route::get('/products/keyword-suggestions', 'productKeywordSuggestions');
        Route::get('/shop/products', 'shopProducts');
        Route::get('/shop/product/{slug}', 'shopProductDetails')->name('ecom-product.details');
        Route::get('products/shop-for-me', 'shopForMe');
        Route::get('/shop/{id}/products/for-you', 'forYou');
    });


    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/product/{slug}/comments/store', [ProductCommentController::class, 'productCommentStore']);
        Route::get('/product/{slug}/my-comments', [ProductCommentController::class, 'productMyComments']);
    });
});
Route::get('/public/shop-products', [PublicProductController::class, 'index'])->name('public.shop-products')->middleware('throttle:60,1');
//Route::get('/public/ratings', [PublicReviewController::class, 'index'])->name('public.ratings')->middleware('throttle:60,1');
Route::get('/public/events', [PublicEventController::class, 'index'])->name('public.events')->middleware('throttle:60,1');
//Route::get('/product/{slug}/comments', [ProductCommentController::class, 'productComments']);
