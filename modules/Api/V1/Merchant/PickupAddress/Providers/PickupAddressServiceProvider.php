<?php

namespace Modules\Api\V1\Merchant\PickupAddress\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class PickupAddressServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Route::prefix('api/v1/merchant')
             ->middleware('api')
             ->as('api.v1.merchant.')
             ->namespace('Modules\Api\V1\Merchant\PickupAddress\Http\Controllers')
             ->group(__DIR__ . '/../Routes/api.php');
    }
}