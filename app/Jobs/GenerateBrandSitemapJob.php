<?php

namespace App\Jobs;

use App\Models\Brand\Brand;
use App\Caches\ShopSettingsCache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class GenerateBrandSitemapJob implements ShouldQueue
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
            $brands = Brand::select('id', 'name')->get();

            $diskName = config("filesystems.default");
            $frontendUrl = ShopSettingsCache::findByKey('app_e_commerce_url');
            //first delete old sitemap index
            Storage::disk($diskName)->delete('sitemaps/sitemap_brands.xml'); 
            $xml = view('sitemaps.brands', [
                'brands' => $brands,
                'frontendUrl' => $frontendUrl,
            ])->render();

            Storage::disk($diskName)->put(
                'sitemaps/sitemap_brands.xml',
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
