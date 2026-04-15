<?php

namespace App\Models\PrimeView;

use App\Media\HasMedia;
use App\Media\Mediable;
use App\Models\Campaign\Campaign;
use App\Models\Product\Product;
use App\Traits\HasTimezone;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Activity;

class PrimeView extends Model implements Mediable
{
    use HasMedia, HasTimezone;

    protected $connection = 'mysql_internal';

    protected $guarded = [];

    protected $appends = ['background', 'menu_icon'];

    protected $hidden = ['media'];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }

    public function setNameAttribute($value): void
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }

    public function getBackgroundAttribute(): string
    {
        return $this->getFirstUrl('background', env('APP_URL'));
    }

    /**
     * @throws Exception
     */
    public function setBackgroundAttribute($file): void
    {
        if ($file) {
            $this->deleteMediaCollection('background');
            $this->addMedia($file, 'background');
        }
    }

    public function getMenuIconAttribute(): string
    {
        return $this->getFirstUrl('menu_icon', env('APP_URL'));
    }

    /**
     * @throws Exception
     */
    public function setMenuIconAttribute($file): void
    {
        if ($file) {
            $this->deleteMediaCollection('menu_icon');
            $this->addMedia($file, 'menu_icon');
        }
    }

    public function primeViewProducts(): Builder|HasMany|PrimeView
    {
        return $this->hasMany(PrimeViewProduct::class);
    }

    public function activities(): MorphMany
    {
        return $this->morphMany(Activity::class, 'subject');
    }

    public function campaigns(): BelongsToMany
    {
        return $this->belongsToMany(Campaign::class)
            ->withPivot(['discount_amount', 'discount_type', 'rules'])
            ->withTimestamps()->withTrashed();
    }

}
