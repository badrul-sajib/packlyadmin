<?php

namespace App\Caches;

use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class ProductListingCache
{
    private const CACHE_TTL = 30 * 60; // 30 minutes
    public const CACHE_PREFIX = 'ps_';

    public function get(string $key, Closure $callback): mixed
    {
        return Cache::remember($key,  self::CACHE_TTL, $callback);
    }

    public function invalidate(): void
    {
        $driver = config('cache.default');
        match ($driver) {
            'redis' => $this->invalidateRedis(),
            'database', 'file' => Cache::flush(),
            default => Cache::flush(),
        };
    }

    private function invalidateRedis(): void
    {
        $prefix =  config('cache.prefix');
        $keys   = Redis::keys("{$prefix}:".self::CACHE_PREFIX.'*');

        if (! empty($keys)) {
            foreach ($keys as $key) {
                Redis::del(str_replace("{$prefix}:", '', $key));
            }
        }
    }
}
