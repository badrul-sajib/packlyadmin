<?php

namespace App\Models\Setting;

use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ShopSetting extends Model
{
    use HasTimezone;

    protected $connection = 'mysql_internal';

    public function getValueAttribute()
    {
        $raw = $this->attributes['value'] ?? null;
        if ($this->type === 'file' && $raw) {
            return Storage::disk(config('filesystems.default') ?? 'public')->url($raw);
        }

        return $raw;
    }
}
