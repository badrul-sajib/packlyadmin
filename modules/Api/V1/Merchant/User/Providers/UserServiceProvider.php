<?php

namespace Modules\Api\V1\Merchant\User\Providers;

use Illuminate\Support\ServiceProvider;

use Modules\Api\V1\Merchant\User\Repositories\UserRepository;
use Modules\Api\V1\Merchant\User\Repositories\Contracts\UserRepositoryInterface;
use Modules\Api\V1\Merchant\User\Services\UserService;


class UserServiceProvider extends ServiceProvider
{
    protected string $modulePath = __DIR__ . '/../';

    public function register(): void
    {
        $this->app->bind(
            UserRepositoryInterface::class,
            UserRepository::class
        );

        $this->app->singleton(
            UserService::class,
            fn ($app) => new UserService(
                $app->make(UserRepositoryInterface::class)
            )
        );
    }

    public function boot(): void
    {
        $this->registerRoutes();
        $this->registerViews();
        $this->registerMigrations();
        $this->registerConfig();
    }

    protected function registerRoutes(): void
    {
        $web = $this->modulePath . 'Routes/web.php';
        $api = $this->modulePath . 'Routes/api.php';

        if (file_exists($web)) {
            $this->loadRoutesFrom($web);
        }
        if (file_exists($api)) {
            $this->loadRoutesFrom($api);
        }
    }

    protected function registerViews(): void
    {
        $path = $this->modulePath . 'Resources/views';
        if (is_dir($path)) {
            $this->loadViewsFrom($path, 'merchant/user');
        }
    }

    protected function registerMigrations(): void
    {
        $path = $this->modulePath . 'Migrations';
        if (is_dir($path)) {
            $this->loadMigrationsFrom($path);
        }
    }

    protected function registerConfig(): void
    {
        $path = $this->modulePath . 'Config/config.php';
        if (file_exists($path)) {
            $this->mergeConfigFrom($path, 'modules.api.v1.merchant.user');
        }
    }
}
