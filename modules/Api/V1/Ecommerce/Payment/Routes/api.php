<?php

use Modules\Api\V1\Ecommerce\Payment\Http\Controllers\PaymentController;
use Modules\Api\V1\Ecommerce\Payment\Http\Controllers\PaymentMethodController;
use Modules\Api\V1\Ecommerce\Payment\Http\Controllers\SslcommerzPaymentController;


Route::middleware(['api.check', 'auth:sanctum'])->group(function () {
    Route::get('/payment/order/{order}', [PaymentController::class, 'payment']);
    Route::get('/sslcommerz/create/{order}', [SslcommerzPaymentController::class, 'create']);
});

Route::middleware(['api.check'])->group(function () {
    Route::get('/payment-methods', [PaymentMethodController::class, 'index']);
});