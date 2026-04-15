<?php

namespace Modules\Api\V1\Merchant\Warehouse\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class WarehouseServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Route::prefix('api/v1/merchant')
             ->middleware('api')
             ->as('api.v1.merchant.')
             ->namespace('Modules\Api\V1\Merchant\Warehouse\Http\Controllers')
             ->group(__DIR__ . '/../Routes/api.php');
    }
}