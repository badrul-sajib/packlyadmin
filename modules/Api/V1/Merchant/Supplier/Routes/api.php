<?php

use Modules\Api\V1\Merchant\Supplier\Http\Controllers\SupplierController;


Route::middleware(['auth:sanctum', 'token.expiration', 'hit.control', 'role.check', 'business.manager.api.check'])->group(function () {
    Route::apiResource('suppliers', SupplierController::class);

    // Additional supplier routes
    Route::get('/supplier-purchase-due-report/{id}', [SupplierController::class, 'supplierDueReport']);
    Route::post('/supplier-due-payment', [SupplierController::class, 'duePayment']);
    Route::get('/supplier-purchases/{supplier_id}', [SupplierController::class, 'supplierPurchases']);
    Route::get('/supplier-transactions/{supplier_id}', [SupplierController::class, 'supplierTransaction']);
});
