<?php

namespace App\Jobs;

use App\Caches\ShopSettingsCache;
use App\Services\CategoryService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class GenerateCategorySitemapJob implements ShouldQueue
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
            $categories = CategoryService::categoryQuery();

            $diskName = config("filesystems.default");
            $frontendUrl = ShopSettingsCache::findByKey('app_e_commerce_url');
            //first delete old sitemap index
            Storage::disk($diskName)->delete('sitemaps/sitemap_categories.xml'); 
            $xml = view('sitemaps.categories', [
                'categories' => $categories,
                'frontendUrl' => $frontendUrl,
            ])->render();

            Storage::disk($diskName)->put(
                'sitemaps/sitemap_categories.xml',
                $xml,
                [
                    'visibility' => 'public',
                    'ContentType'  => 'application/xml',
                    'ContentDisposition' => 'inline',
                    'CacheControl' => 'no-cache, no-store, must-revalidate',
                ]
            );
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }
}
