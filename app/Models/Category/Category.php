<?php

namespace App\Models\Category;

use App\Media\HasMedia;
use App\Media\Mediable;
use App\Models\Coupon\Coupon;
use App\Models\Product\Product;
use App\Traits\HasTimezone;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Category extends Model implements Mediable
{
    use HasMedia, HasTimezone, LogsActivity;

    protected $connection = 'mysql_internal';

    protected $fillable = ['name', 'slug', 'status', 'added_by', 'image','commission', 'commission_type'];

    protected $hidden = ['media'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['commission', 'commission_type'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function setNameAttribute($value): void
    {
        $this->attributes['name'] = ucwords($value);
        $this->attributes['slug'] = Str::slug($value);
    }

    /**
     * @throws Exception
     */
    public function setImageAttribute($file): void
    {
        if ($file) {
            if ($this->media()->where('collection_name', 'images')->first()) {
                $this->deleteMedia($this->media()->where('collection_name', 'images')->first()->id);
            }
            $this->addMedia($file, 'images', ['tags' => '']);
        }
    }

    public function getImageAttribute(): string
    {
        return $this->getFirstUrl('images', env('APP_URL'));
    }

    public function products(): Builder|HasMany|Category
    {
        return $this->hasMany(Product::class);
    }

    public function coupons(): BelongsToMany
    {
        return $this->belongsToMany(Coupon::class, 'category_coupons');
    }

    public function subcategories(): Builder|HasMany|Category
    {
        return $this->hasMany(SubCategory::class);
    }
}
