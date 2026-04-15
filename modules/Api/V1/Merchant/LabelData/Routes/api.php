<?php

use Modules\Api\V1\Merchant\LabelData\Http\Controllers\LabelDataController;

Route::middleware(['auth:sanctum', 'token.expiration', 'hit.control', 'role.check', 'business.manager.api.check'])->group(function () {
    Route::get('/label-data', [LabelDataController::class, 'getLabel']);
});
