<?php

use Modules\Api\V1\Ecommerce\Customer\Http\Controllers\CustomerController;


Route::middleware(['api.check', 'auth:sanctum'])->group(function () {
    Route::controller(CustomerController::class)->group(function () {
        Route::post('/customer-address/send-otp', 'customerAddressSendOtp');
        Route::post('/customer-address/verify-otp', 'customerAddressVerifyOtp');
        Route::post('/customer-address/store', 'customerAddressStore');
        Route::get('/customer-address/list', 'customerAddressList');
        Route::post('/customer-address/update/{id}', 'customerAddressUpdate');
        Route::get('/customer/orders', 'customerOrders');
        Route::get('/customer/orders/{tracking_id}', 'customerOrderDetails');
        Route::post('/order/{tracking_id}/items/cancel', 'cancelOrderItem');
        Route::post('/order/{tracking_id}/items/return', 'returnOrderItem');
        Route::get('/my/returns', 'customerReturns');
        Route::get('/my/return/{item_id}/{tracking_id}', 'returnDetails');
        Route::post('/payment/success', 'paymentSuccess');
        Route::get('/customer/gtm/info', 'customerGtmInfo');
    });
});
