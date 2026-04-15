<?php

use Modules\Api\V1\Ecommerce\PrimeView\Http\Controllers\PrimeViewController;


Route::middleware('api.check')->group(function () {
    Route::controller(PrimeViewController::class)->group(function () {
        Route::get('/prime-view', 'primeView');
        Route::get('/sticky-menu', 'stickyMenu');
        Route::get('/explore-item', 'exploreItem');
        Route::get('/trending-category-header', 'trendingCategoryHeader');
    });
});