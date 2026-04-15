<?php

use Modules\Api\V1\Merchant\Wallet\Http\Controllers\WalletController;


Route::middleware(['auth:sanctum', 'token.expiration', 'hit.control', 'role.check', 'business.manager.api.check'])->group(function () {
    Route::get('/wallets', [WalletController::class, 'index']);
    Route::post('/wallets', [WalletController::class, 'store']);
    Route::put('/wallets/{id}', [WalletController::class, 'update']);
    Route::delete('/wallets/{id}', [WalletController::class, 'destroy']);

    // Additional wallet routes
    Route::patch('/change-wallet-status/{id}', [WalletController::class, 'status']);
    Route::get('/wallets/{id}/transactions', [WalletController::class, 'getTransactions']);
});
