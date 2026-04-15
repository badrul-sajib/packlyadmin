<?php

namespace App\Services\Cache;

use Illuminate\Support\Facades\Cache;

class CustomCache extends BaseCacheService
{
    public function getAll(string $key, $default = null)
    {
        return $this->get($key, $default);
    }

    public function setAll(string $key, $value, $ttl = null): void
    {
        $this->store($key, $value, $ttl);
    }

    public function forgetAll(string $key): void
    {
        $this->forget($key);
    }

    public function executeWithCache(string $cacheKey, callable $queryCallback, ?int $ttl = null)
    {
        return Cache::store($this->connection)->remember(
            $this->generateKey($cacheKey),
            $ttl ?? $this->defaultTTL,
            $queryCallback
        );
    }
}
