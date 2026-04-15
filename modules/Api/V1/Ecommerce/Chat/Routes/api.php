<?php

use Modules\Api\V1\Ecommerce\Chat\Http\Controllers\ChatController;


Route::middleware(['api.check', 'auth:sanctum'])->group(function () {
    Route::controller(ChatController::class)->group(function () {
        Route::get('/chats', 'index');
        Route::get('/chats/users', 'users');
        Route::get('/chats/{id}', 'show');
        Route::post('/chats/send-message', 'sendMessage');
        Route::post('/chats/mark-as-seen', 'markAsSeen');
        Route::post('/chats/block-user/{id}', 'blockUser');
        Route::post('/chats/unblock-user/{id}', 'unblockUser');
    });
});