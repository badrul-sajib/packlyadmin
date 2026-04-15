<?php

use Modules\Api\V1\Ecommerce\Voucher\Http\Controllers\VoucharController;


Route::middleware('api.check')->group(function () {
    Route::controller(VoucharController::class)->group(function () {
        Route::get('/vouchers', 'index');
        Route::get('/merchant-vouchers/{id}', 'merchantVouchers');
    });
});