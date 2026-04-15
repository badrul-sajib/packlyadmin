<?php

namespace Modules\Api\V1\Merchant\Sale\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class SaleServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Route::prefix('api/v1/merchant')
             ->middleware('api')
             ->as('api.v1.merchant.')
             ->namespace('Modules\Api\V1\Merchant\Sale\Http\Controllers')
             ->group(__DIR__ . '/../Routes/api.php');
    }
}