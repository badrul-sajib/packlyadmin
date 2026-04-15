<?php

namespace App\Jobs;

use App\Models\Product\Product;
use App\Caches\ShopSettingsCache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class GenerateProductSitemapJob implements ShouldQueue
{
    use Queueable;

    public $page; 
    public $perPage; 
    /**
     * Create a new job instance.
     */
    public function __construct($page,$perPage)
    {
        $this->page = $page;
        $this->perPage = $perPage;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $products  = Product::baseShopQuery()
                ->skip(($this->page - 1) * $this->perPage)
                ->take($this->perPage)
                ->get();

            $diskName = config("filesystems.default");
            $app_e_commerce_url = ShopSettingsCache::findByKey('app_e_commerce_url');

            //first delete old products sitemap index
            Storage::disk($diskName)->delete("sitemaps/products/sitemap_{$this->page}.xml");

            $xml = view('sitemaps.products', [
                'products'           => $products,
                'app_e_commerce_url' => $app_e_commerce_url,
            ])->render();

            Storage::disk($diskName)->put(
                "sitemaps/products/sitemap_{$this->page}.xml",
                $xml,
                [
                    'visibility' => 'public',
                    'ContentType'  => 'application/xml',
                    'CacheControl' => 'no-cache, no-store, must-revalidate',
                ]
            );

        } catch (\Throwable $th) {
            Log::error("GenerateProductSitemapJob::handle - Error: {$th->getMessage()}");
        }
        
    }

}
