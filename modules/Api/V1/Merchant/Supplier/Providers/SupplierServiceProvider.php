<?php

namespace Modules\Api\V1\Merchant\Supplier\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class SupplierServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Route::prefix('api/v1/merchant')
             ->middleware('api')
             ->as('api.v1.merchant.')
             ->namespace('Modules\Api\V1\Merchant\Supplier\Http\Controllers')
             ->group(__DIR__ . '/../Routes/api.php');
    }
}