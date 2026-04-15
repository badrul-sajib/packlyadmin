<?php

use Modules\Api\V1\Ecommerce\Order\Http\Controllers\OrderController;


Route::middleware(['api.check', 'auth:sanctum'])->group(function () {
    Route::controller(OrderController::class)->group(function () {
        Route::get('/orders', 'shopOrders');
        Route::get('/order/cancel/{item_id}', 'orderCancelDetails');
        Route::get('/order/buyagain/{order_id}', 'buyAgain');
        Route::get('/to-pay/orders', 'toPayOrders');
        Route::get('/to-pay/order-detail/{order_id}', 'toPayOrderDetails');
        Route::get('/order/counts', 'orderCounts');
        Route::get('/order/item/{orderItem}', 'orderItem');
        Route::get('/all/order/list', 'allOrderList');
    });
});

Route::middleware(['api.check'])->group(function () {
    Route::get('/order/{tracking_id}/track', [OrderController::class, 'orderDetails']);
});