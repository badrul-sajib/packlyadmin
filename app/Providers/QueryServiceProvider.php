<?php

namespace App\Providers;

use App\Enums\ShopProductStatus;
use App\Models\Shop\ShopProduct;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class QueryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $pending_product_req = 0;
        if (Schema::hasTable('shop_products')) {
            $pending_product_req = ShopProduct::where('status', ShopProductStatus::PENDING->value)->count();
        }
        view()->share('pending_product_req', $pending_product_req);
    }
}
