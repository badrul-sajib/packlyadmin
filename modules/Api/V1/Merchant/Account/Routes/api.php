<?php

use Modules\Api\V1\Merchant\Account\Http\Controllers\AccountChartController;
use Modules\Api\V1\Merchant\Account\Http\Controllers\AccountController;
use Modules\Api\V1\Merchant\Account\Http\Controllers\AccountTransferController;
use Modules\Api\V1\Merchant\Account\Http\Controllers\BeneficiaryAccountController;
use Modules\Api\V1\Merchant\Account\Http\Controllers\ExpenseController;
use Modules\Api\V1\Merchant\Account\Http\Controllers\IncomeController;
use Modules\Api\V1\Merchant\Account\Http\Controllers\JournalController;
use Modules\Api\V1\Merchant\Account\Http\Controllers\OwnerContributionController;
use Modules\Api\V1\Merchant\Account\Http\Controllers\WithdrawController;


Route::middleware(['auth:sanctum', 'token.expiration', 'hit.control', 'business.manager.api.check'])->group(function () {
    Route::controller(AccountController::class)->group(function () {
        Route::get('/total-balance', 'totalBalance');
        Route::get('/accounts', 'index');
        Route::post('/accounts', 'store');
        Route::put('/account/{account}', 'update');
    });
});

Route::middleware(['auth:sanctum', 'token.expiration', 'hit.control', 'role.check'])->group(function () {
    Route::patch('/change-beneficiary-status/{id}', [BeneficiaryAccountController::class, 'status']);
    Route::apiResource('beneficiary-accounts', BeneficiaryAccountController::class)->except(['create', 'edit']);
    Route::get('/accounts-by-account-type/{id}', [AccountChartController::class, 'chartAccountsByAccountType']);
    Route::get('/all-accounts-by-expense-type', [AccountChartController::class, 'chartAccountsByExpenseAccountType']);

    // Income
    Route::get('/income', [IncomeController::class, 'index']);
    Route::post('/income', [IncomeController::class, 'store']);

    // Expense
    Route::post('/expenses', [ExpenseController::class, 'store']);

    // Account Transfer
    Route::get('/account-transfers', [AccountTransferController::class, 'index']);
    Route::post('/account-transfers', [AccountTransferController::class, 'store']);

    // Owner Withdraw
    Route::get('/owner-withdraw', [WithdrawController::class, 'index']);
    Route::post('/owner-withdraw', [WithdrawController::class, 'store']);

    // Owner Contribution
    Route::get('/owner-contribution', [OwnerContributionController::class, 'index']);
    Route::post('/owner-contribution', [OwnerContributionController::class, 'store']);

    // Journal
    Route::get('/journal', [JournalController::class, 'index']);
    Route::post('/journal', [JournalController::class, 'store']);
});
