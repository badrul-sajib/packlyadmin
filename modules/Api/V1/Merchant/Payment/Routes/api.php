<?php

use Modules\Api\V1\Merchant\Payment\Http\Controllers\PaymentController;
use Modules\Api\V1\Merchant\Payment\Http\Controllers\PayoutBeneficiaryBankController;
use Modules\Api\V1\Merchant\Payment\Http\Controllers\PayoutBeneficiaryController;
use Modules\Api\V1\Merchant\Payment\Http\Controllers\PayoutBeneficiaryMobileWalletController;
use Modules\Api\V1\Merchant\Payment\Http\Controllers\PayoutController;


Route::middleware(['auth:sanctum', 'token.expiration', 'hit.control', 'role.check', 'business.manager.api.check'])->group(function () {
    // Payment routes
    Route::get('/payout-beneficiary-mobile-wallets', [PayoutBeneficiaryMobileWalletController::class, 'index']);
    Route::get('/payout-beneficiary-banks', [PayoutBeneficiaryBankController::class, 'index']);
    Route::get('payout-beneficiaries/mobile-wallet-wise', [PayoutBeneficiaryMobileWalletController::class, 'mobileWalletWisePayoutBeneficiary']);
    Route::post('/sell-payment', [PaymentController::class, 'storeSellPayment']);
    Route::post('purchase-payment', [PaymentController::class, 'storePurchasePayment']);

    Route::controller(PayoutBeneficiaryController::class)->group(function () {
        Route::get('payout-beneficiaries/payout-recurring-types', 'payoutRecurringTypes');
        Route::get('payout-beneficiaries', 'index');
        Route::get('payout-beneficiaries/{id}', 'show');
        Route::patch('payout-beneficiaries/{payoutBeneficiary?}/default', 'setDefault');
        Route::post('payout-beneficiaries/{payoutBeneficiary?}', 'store');
    });

    // Payout Request
    Route::get('/payout-requests', [PayoutController::class, 'index']);
    Route::get('/payout-requests/{id}', [PayoutController::class, 'show']);
    Route::post('/payout-requests', [PayoutController::class, 'store']);
    Route::get('/payout-eligible-merchant-orders', [PayoutController::class, 'payoutEligibleMerchantOrders']);
    Route::get('/payout-merchant-orders', [PayoutController::class, 'payoutMerchantOrders']);
});
