<?php

namespace App\Http\Controllers\Admin\Cache;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Artisan;

class CacheController extends Controller
{
    public function index()
    {
        $caches = [];
        $redis  = config('cache.default') == 'redis' ? true : false;
        if ($redis) {
            $caches = $this->getCategorizedCacheData();
        }

        return view('Admin::caches.index', compact('caches', 'redis'));
    }

    private function getCategorizedCacheData()
    {
        $redis   = Redis::connection('cache');
        $allKeys = $redis->keys('*');

        // Initialize categories
        $categories = [
            'products'        => [],
            'product_details' => [],
            'currency'        => [],
            'categories'      => [],
            'pages'           => [],
            'sliders'         => [],
            'other'           => [],
        ];

        // Process each cache key
        foreach ($allKeys as $key) {
            // Get key info
            $withoutPrefix = $key;
            $ttl           = $redis->ttl('cache_'.$withoutPrefix);
            $size          = $this->getCacheSize($redis, 'cache_'.$withoutPrefix);

            if ($ttl > 0) {
                $expiresAt       = now()->addSeconds($ttl);
                $expiryFormatted = $expiresAt->format('Y-m-d H:i:s');
                $expiryRelative  = $expiresAt->diffForHumans();
            } elseif ($ttl == -1) {
                $expiryFormatted = 'Never';
                $expiryRelative  = 'No expiration';
            } else {
                $expiryFormatted = 'Expired';
                $expiryRelative  = 'Already expired';
            }

            // Create base cache info object
            $cacheInfo = (object) [
                'key'              => $key,
                'name'             => $withoutPrefix,
                'size'             => $this->formatSize($size),
                'size_bytes'       => $size,
                'ttl'              => $ttl,
                'ttl_formatted'    => $ttl > 0 ? $this->formatTtl($ttl) : ($ttl == -1 ? 'No Expiry' : 'Expired'),
                'expires_at'       => $expiryFormatted,
                'expires_relative' => $expiryRelative,
            ];

            // Categorize by type
            if (strpos($withoutPrefix, 'products_') === 0) {
                // Match product listing caches
                if (preg_match('/^products_(.*?)_page_(\d+)_limit_(\d+)$/u', $withoutPrefix, $matches)) {
                    $cacheInfo->category      = $matches[1];
                    $cacheInfo->page          = (int) $matches[2];
                    $cacheInfo->limit         = (int) $matches[3];
                    $cacheInfo->type          = 'Product Listing';
                    $categories['products'][] = $cacheInfo;
                } else {
                    $categories['other'][] = $cacheInfo;
                }
            } elseif (strpos($withoutPrefix, 'product_') === 0) {
                // Single product caches
                $productId                       = substr($withoutPrefix, 8);
                $cacheInfo->product_id           = $productId;
                $cacheInfo->type                 = 'Product Detail';
                $categories['product_details'][] = $cacheInfo;
            } elseif (strpos($withoutPrefix, 'currency') !== false) {
                $cacheInfo->type          = 'Currency';
                $categories['currency'][] = $cacheInfo;
            } elseif (strpos($withoutPrefix, 'Categories') !== false) {
                $cacheInfo->type            = 'Categories';
                $categories['categories'][] = $cacheInfo;
            } elseif ($withoutPrefix === 'pages') {
                $cacheInfo->type       = 'Pages';
                $categories['pages'][] = $cacheInfo;
            } elseif ($withoutPrefix === 'sliders') {
                $cacheInfo->type         = 'Sliders';
                $categories['sliders'][] = $cacheInfo;
            } else {
                $cacheInfo->type       = 'Other';
                $categories['other'][] = $cacheInfo;
            }
        }

        // Calculate summary statistics
        $summary = [
            'total_keys' => count($allKeys),
            'total_size' => $this->formatSize(array_sum(array_map(function ($key) use ($redis) {
                return $this->getCacheSize($redis, $key);
            }, $allKeys))),
            'category_counts' => [
                'products'        => count($categories['products']),
                'product_details' => count($categories['product_details']),
                'currency'        => count($categories['currency']),
                'categories'      => count($categories['categories']),
                'pages'           => count($categories['pages']),
                'sliders'         => count($categories['sliders']),
                'other'           => count($categories['other']),
            ],
        ];

        return [
            'categories' => $categories,
            'summary'    => $summary,
        ];
    }

    private function getCacheSize($redis, $key)
    {
        try {
            $info = $redis->rawCommand('MEMORY', 'USAGE', $key);

            return $info ?? strlen($redis->get($key) ?? '');
        } catch (\Exception $e) {
            // Fallback method if MEMORY USAGE isn't available
            return strlen($redis->get($key) ?? '');
        }
    }

    private function formatSize($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $index = 0;

        while ($bytes >= 1024 && $index < count($units) - 1) {
            $bytes /= 1024;
            $index++;
        }

        return round($bytes, 2).' '.$units[$index];
    }

    private function formatTtl($seconds)
    {
        $days = floor($seconds / 86400);
        $seconds %= 86400;

        $hours = floor($seconds / 3600);
        $seconds %= 3600;

        $minutes = floor($seconds / 60);
        $seconds %= 60;

        $parts = [];
        if ($days > 0) {
            $parts[] = $days.'d';
        }
        if ($hours > 0) {
            $parts[] = $hours.'h';
        }
        if ($minutes > 0) {
            $parts[] = $minutes.'m';
        }
        if ($seconds > 0 || count($parts) === 0) {
            $parts[] = $seconds.'s';
        }

        return implode(' ', $parts);
    }

    public function clearAll()
    {
        try {
            if (config('cache.default') == 'redis') {
                $redis = Redis::connection('cache');
                $redis->flushDB();
            } else {
                Cache::flush();
            }
            Artisan::call('cache:clear');
        } catch (\Exception $e) {
            Log::error('Failed to clear all caches: '.$e->getMessage());
            return redirect()->route('admin.caches.index')->with('error', 'Failed to clear all caches');
        }

        return redirect()->back()->with('success', 'All caches cleared successfully.');
    }

    public function destroy($key)
    {
        try {
            $redis = Redis::connection('cache');
            $redis->del($key);
        } catch (\Exception $e) {
            Log::error('Failed to delete cache: '.$e->getMessage());
            return redirect()->route('admin.caches.index')->with('error', 'Failed to delete cache');
        }

        return redirect()->back()->with('success', 'Cache deleted successfully.');
    }
}
