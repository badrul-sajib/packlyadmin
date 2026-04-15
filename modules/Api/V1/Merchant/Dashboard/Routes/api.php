<?php

use Modules\Api\V1\Merchant\Dashboard\Http\Controllers\DashboardController;


Route::middleware(['auth:sanctum', 'token.expiration', 'hit.control', 'role.check', 'business.manager.api.check'])->group(function () {
    Route::controller(DashboardController::class)
        ->prefix('dashboard')
        ->group(function () {
            Route::get('/', 'index');
            Route::get('/order-summary', 'orderSummary');
            Route::get('/purchases-sales-expanses-summary', 'purchasesSalesExpansesSummary');
            Route::get('/top-selling-products', 'topSellingProducts');
            Route::get('/top-selling-product-details/{product_id}', 'topSellingProductDetails');
            Route::get('/orders-with-items', 'ordersWithItems');
            Route::get('/sidebar-counts', 'sidebarCounts');
        });
});
