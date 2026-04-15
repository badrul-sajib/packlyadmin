<?php

use Modules\Api\V1\Merchant\MerchantOrder\Http\Controllers\MerchantOrderController;


Route::middleware(['auth:sanctum', 'token.expiration', 'hit.control', 'role.check', 'business.manager.api.check'])->group(function () {
    Route::controller(MerchantOrderController::class)->group(function () {
        Route::get('/orders', 'index');
        Route::get('/order/{merchantOrder}', 'show');
        Route::put('/order/status-change/{merchantOrder}/{status}', 'orderStatusChange');
        Route::put('/order/status-change-bulk', 'bulkOrderStatusChange');
        Route::put('/order/address-update/{merchantOrder}', 'orderAddressUpdate');
        Route::get('/order/tracking/{invoiceNumber}', 'orderTracking');
        Route::get('/order/fraud/checker/{phoneNumber}', 'fraudChecker');
    });
});
