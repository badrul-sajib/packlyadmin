<?php

use Modules\Api\V1\Ecommerce\Version\Http\Controllers\VersionController;


Route::middleware('api.check')->group(function () {
    Route::get('/app-version', [VersionController::class, 'check']);
});