<?php

namespace Modules\Api\V1\Ecommerce\Faq\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class FaqServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Route::prefix('api/v1/ecommerce')
             ->middleware('api')
             ->as('api.v1.ecommerce.')
             ->namespace('Modules\Api\V1\Ecommerce\Faq\Http\Controllers')
             ->group(__DIR__ . '/../Routes/api.php');
    }
}