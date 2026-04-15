<?php

use Modules\Api\V1\Merchant\Notification\Http\Controllers\NotificationController;


Route::post('/notification-webhook', [NotificationController::class, 'webhook']);
Route::middleware(['auth:sanctum', 'token.expiration', 'hit.control', 'business.manager.api.check'])->group(function () {
    Route::controller(NotificationController::class)->prefix('notifications')->group(function () {
        Route::get('/', 'index')->name('notifications.index');
        Route::get('/unread', 'unread');
        Route::post('/mark-as-read/{id}', 'markAsRead')->name('notifications.markAsRead');
        Route::post('/mark-all-read', 'markAllAsRead')->name('notifications.markAllAsRead');
        Route::get('/count', 'getCount')->name('notifications.count');
    });
});
