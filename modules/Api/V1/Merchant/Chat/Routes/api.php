<?php

use Modules\Api\V1\Merchant\Chat\Http\Controllers\ChatController;


Route::middleware(['auth:sanctum', 'token.expiration', 'hit.control', 'role.check', 'business.manager.api.check'])->group(function () {
    Route::get('/chats', [ChatController::class, 'index']);
    Route::get('/chats/users', [ChatController::class, 'users']);
    Route::get('/chats/{id}', [ChatController::class, 'show']);
    Route::post('/chats/send-message', [ChatController::class, 'sendMessage']);
    Route::post('/chats/mark-as-seen', [ChatController::class, 'markAsSeen']);
    Route::post('/chats/block-user/{id}', [ChatController::class, 'blockUser']);
    Route::post('/chats/unblock-user/{id}', [ChatController::class, 'unblockUser']);
});
