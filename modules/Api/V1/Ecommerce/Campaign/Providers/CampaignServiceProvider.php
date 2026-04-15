<?php

namespace Modules\Api\V1\Ecommerce\Campaign\Providers;

use Illuminate\Support\ServiceProvider;

use Modules\Api\V1\Ecommerce\Campaign\Repositories\CampaignRepository;
use Modules\Api\V1\Ecommerce\Campaign\Repositories\Contracts\CampaignRepositoryInterface;
use Modules\Api\V1\Ecommerce\Campaign\Services\CampaignService;


class CampaignServiceProvider extends ServiceProvider
{
    protected string $modulePath = __DIR__ . '/../';

    public function register(): void
    {
        $this->app->bind(
            CampaignRepositoryInterface::class,
            CampaignRepository::class
        );

        $this->app->singleton(
            CampaignService::class,
            fn ($app) => new CampaignService(
                $app->make(CampaignRepositoryInterface::class)
            )
        );
    }

    public function boot(): void
    {
        $this->registerRoutes();
    }

    protected function registerRoutes(): void
    {
        $api = $this->modulePath . 'Routes/api.php';

        if (file_exists($api)) {
            $this->loadRoutesFrom($api);
        }
    }
}
