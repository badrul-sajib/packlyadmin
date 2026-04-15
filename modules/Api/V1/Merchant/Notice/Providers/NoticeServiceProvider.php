<?php

namespace Modules\Api\V1\Merchant\Notice\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class NoticeServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Route::prefix('api/v1/merchant')
            ->middleware('api')
            ->as('api.v1.notice.')
            ->namespace('Modules\Api\V1\Merchant\Notice\Http\Controllers')
            ->group(__DIR__.'/../Routes/api.php');
    }
}
