<?php

namespace App\Jobs;

use App\Caches\ShopSettingsCache;
use App\Jobs\GenerateBrandSitemapJob;
use App\Jobs\GenerateCategorySitemapJob;
use App\Jobs\GenerateProductSitemapJob;
use App\Models\Product\Product;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BuildSitemapIndexJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $perPage        = 1000;
            $totalProducts  = Product::baseShopQuery()->count('products.id');
            $totalPage      = ceil($totalProducts / $perPage);

            $this->storeSitemapIndex($totalPage);
            $this->storeProductCatalogFeed($perPage);

            for ($i = 1; $i <= $totalPage; $i++) {
                GenerateProductSitemapJob::dispatchSync($i,$perPage);
            }

            GenerateCategorySitemapJob::dispatchSync();
            GenerateBrandSitemapJob::dispatchSync();

        } catch (\Throwable $th) {
            Log::error("BuildSitemapIndexJob::handle - Error: {$th->getMessage()}");
        }

    }

    public function storeSitemapIndex($totalPage)
    {
        try {
            $diskName = config("filesystems.default");

            //first delete old sitemap index
            Storage::disk($diskName)->delete('sitemaps/sitemap_index.xml');

            $xml = view('sitemaps.index', [
                'pages' => $totalPage,
            ])->render();

            Storage::disk($diskName)->put(
                'sitemaps/sitemap_index.xml',
                $xml,
                [
                    'visibility' => 'public',
                    'ContentType'  => 'application/xml',
                    'ContentDisposition' => 'inline',
                    'CacheControl' => 'no-cache, no-store, must-revalidate',
                ]
            );
        } catch (\Throwable $th) {
            Log::error("BuildSitemapIndexJob::storeSitemapedIndex - Error: {$th->getMessage()}");
        }
    }

    public function storeProductCatalogFeed(int $perPage): void
    {
        try {
            $diskName        = config('filesystems.default');
            $appEcommerceUrl = ShopSettingsCache::findByKey('app_e_commerce_url');
            $feedPath        = 'sitemaps/product_feed.xml';
            $maxBytes        = 5 * 1024 * 1024; // 5MB

            Storage::disk($diskName)->delete($feedPath);

            $header = implode("\n", [
                '<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">',
                '<channel>',
                '<title>'.e(config('app.name')).' Product Feed</title>',
                '<link>'.e(config('app.url')).'</link>',
                '<description>Product catalog feed for commerce integrations.</description>',
            ]);
            $footer   = "\n</channel>\n</rss>";
            $body     = '';
            $exceeded = false;

            Product::baseShopQuery()
                ->orderBy('products.id')
                ->chunk($perPage, function ($products) use (&$body, &$exceeded, $appEcommerceUrl, $header, $footer, $maxBytes) {
                    if ($exceeded) {
                        return false;
                    }

                    $chunk = view('sitemaps.partials.product-items', [
                        'products'           => $products,
                        'app_e_commerce_url' => $appEcommerceUrl,
                    ])->render();

                    if (strlen($header) + strlen($body) + strlen($chunk) + strlen($footer) > $maxBytes) {
                        $exceeded = true;
                        return false;
                    }

                    $body .= "\n" . $chunk;
                });

            Storage::disk($diskName)->put(
                $feedPath,
                $header . $body . $footer,
                [
                    'visibility'         => 'public',
                    'ContentType'        => 'application/xml',
                    'ContentDisposition' => 'inline',
                    'CacheControl'       => 'no-cache, no-store, must-revalidate',
                ]
            );
        } catch (\Throwable $th) {
            Log::error("BuildSitemapIndexJob::storeProductCatalogFeed - Error: {$th->getMessage()}");
        }
    }
}
