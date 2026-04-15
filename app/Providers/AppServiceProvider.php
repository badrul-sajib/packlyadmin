<?php

namespace App\Providers;

use App\Auth\StaticAdminProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Auth::provider('static_admin', function ($app, array $config) {
            return new StaticAdminProvider();
        });
    }
}
