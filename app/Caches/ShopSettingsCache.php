<?php

namespace App\Caches;

use App\Models\Shop\ShopSetting;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class ShopSettingsCache
{
    private const CACHE_TTL = 30 * 60; // 30 minutes

    public const CACHE_KEY = 'shop_settings';

    public static function get(): mixed
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return ShopSetting::pluck('value', 'key')->toArray() ?? [];
        });
    }

    public static function findByKey(string $key): mixed
    {
        return self::get()[$key] ?? '';
    }

    public static function select(string ...$keys): mixed
    {
        $allSettings = self::get();
        $result = new \stdClass;

        foreach ($keys as $key) {
            if ($allSettings[$key]) {
                $result->$key = $allSettings[$key] ?? '';
            } else {
                throw new Exception('Shop Setting Key not found: ' . $key);
            }
        }

        return $result;
    }

    public static function invalidate(): void
    {
        $driver = config('cache.default');
        match ($driver) {
            'redis' => Redis::del(self::CACHE_KEY),
            'database', 'file' => Cache::forget(self::CACHE_KEY),
            default => Cache::forget(self::CACHE_KEY),
        };

        match ($driver) {
            'redis' => Redis::del('settings'),
            'database', 'file' => Cache::forget('settings'),
            default => Cache::forget('settings'),
        };

        Cache::forget(self::CACHE_KEY);
        Cache::forget('settings');
    }
}
