<?php

use Modules\Api\V1\Merchant\PickupAddress\Http\Controllers\PickupAddressController;



Route::middleware(['auth:sanctum', 'token.expiration', 'hit.control', 'business.manager.api.check'])->group(function () {
    Route::controller(PickupAddressController::class)
        ->prefix('pickup-address')
        ->group(function () {
            Route::get('/', 'index');
            Route::get('show', 'show');
            Route::post('store', 'store');
            Route::put('update/{pickupAddress}', 'update');
            Route::post('send/{pickupAddress}', 'sendPickupAddressRequest');
            Route::get('get-police-stations', 'getPoliceStations');
            Route::get('requests', 'pickupAddressRequests');
        });
});
