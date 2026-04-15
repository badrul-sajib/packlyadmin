<?php

namespace App\Models\Category;

use App\Media\HasMedia;
use App\Media\Mediable;
use App\Models\Product\Product;
use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class SubCategory extends Model implements Mediable
{
    use HasMedia, HasTimezone, LogsActivity;

    protected $connection = 'mysql_internal';

    protected $fillable = ['name', 'slug', 'category_id', 'status', 'added_by', 'image','commission', 'commission_type'];

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

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function subchilds(): HasMany
    {
        return $this->hasMany(SubCategoryChild::class);
    }

    public function setImageAttribute($file)
    {
        if ($file) {
            if ($this->media()->where('collection_name', 'images')->first()) {
                $this->deleteMedia($this->media()->where('collection_name', 'images')->first()->id);
            }

            $this->addMedia($file, 'images', ['tags' => '']);
        }
    }

    public function getImageAttribute()
    {
        return $this->getFirstUrl('images', env('APP_URL'));
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
