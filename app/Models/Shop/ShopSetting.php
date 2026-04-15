<?php

namespace App\Models\Shop;

use App\Caches\ShopSettingsCache;
use App\Traits\HasTimezone;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ShopSetting extends Model
{
    use HasTimezone;

    protected $connection = 'mysql_internal';

    // You may want to use $fillable instead of $guarded for security reasons.
    protected $guarded = [];

    public function getValueAttribute()
    {
        $raw = $this->attributes['value'] ?? null;
        if ($this->type === 'file' && $raw) {
            return Storage::disk(config('filesystems.default') ?? 'public')->url($raw);
        }

        return $raw;
    }

    // The boot method should be static as it is a part of Eloquent lifecycle
    protected static function boot(): void
    {
        parent::boot();

        static::created(function ($model) {
            // Clear cache when a new setting is created
            ShopSettingsCache::invalidate();
        });

        static::updated(function ($model) {
            // Clear cache when a setting is updated
            ShopSettingsCache::invalidate();
        });

        static::deleted(function ($model) {
            // Clear cache when a setting is deleted
            ShopSettingsCache::invalidate();
        });
    }
}
