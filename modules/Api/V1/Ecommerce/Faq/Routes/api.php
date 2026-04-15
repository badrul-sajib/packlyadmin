<?php

use Modules\Api\V1\Ecommerce\Faq\Http\Controllers\FaqController;


Route::middleware(['api.check'])->group(function () {
    Route::get('faqs', [FaqController::class, 'index']);
});