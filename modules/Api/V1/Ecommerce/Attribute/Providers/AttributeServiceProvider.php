<?php

namespace Modules\Api\V1\Ecommerce\Attribute\Providers;

use Illuminate\Support\ServiceProvider;

use Modules\Api\V1\Ecommerce\Attribute\Repositories\AttributeRepository;
use Modules\Api\V1\Ecommerce\Attribute\Repositories\Contracts\AttributeRepositoryInterface;
use Modules\Api\V1\Ecommerce\Attribute\Services\AttributeService;


class AttributeServiceProvider extends ServiceProvider
{
    protected string $modulePath = __DIR__ . '/../';

    public function register(): void
    {
        $this->app->bind(
            AttributeRepositoryInterface::class,
            AttributeRepository::class
        );

        $this->app->singleton(
            AttributeService::class,
            fn ($app) => new AttributeService(
                $app->make(AttributeRepositoryInterface::class)
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
            $this->loadViewsFrom($path, 'ecommerce/attribute');
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
            $this->mergeConfigFrom($path, 'modules.api.v1.ecommerce.attribute');
        }
    }
}
