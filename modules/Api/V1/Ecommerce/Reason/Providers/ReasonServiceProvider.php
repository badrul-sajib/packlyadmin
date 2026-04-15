<?php

namespace Modules\Api\V1\Ecommerce\Reason\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class ReasonServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Route::prefix('api/v1/ecommerce')
             ->middleware('api')
             ->as('api.v1.ecommerce.')
             ->namespace('Modules\Api\V1\Ecommerce\Reason\Http\Controllers')
             ->group(__DIR__ . '/../Routes/api.php');
    }
}