<?php

namespace App\Models\Slider;

use App\Enums\SliderType;
use App\Media\HasMedia;
use App\Media\Mediable;
use App\Models\Product\Product;
use App\Traits\HasTimezone;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Slider extends Model implements Mediable
{
    use HasMedia, HasTimezone;

    protected $connection = 'mysql_internal';

    protected $guarded = [];

    protected $appends = ['full_image', 'small_image', 'label', 'mobile_label_banner', 'desktop_label_banner'];

    protected $hidden = ['media'];

    protected $casts = [
        'slider_type' => SliderType::class,
    ];

    public function getFullImageAttribute(): string
    {
        return $this->getFirstUrl('web_image', env('APP_URL'));
    }

    public function getSmallImageAttribute(): string
    {
        return $this->getFirstUrl('mobile_image', env('APP_URL'));
    }

    public function getDesktopLabelBannerAttribute(): string
    {
        return $this->getFirstUrl('desktop_label_banner', env('APP_URL'));
    }

    public function getMobileLabelBannerAttribute(): string
    {
        return $this->getFirstUrl('mobile_label_banner', env('APP_URL'));
    }

    /**
     * @throws Exception
     */
    public function setFullImageAttribute($file): void
    {
        if ($file) {
            if ($this->media()->where('collection_name', 'web_image')->first()) {
                $this->deleteMedia($this->media()->where('collection_name', 'web_image')->first()->id);
            }

            $this->addMedia($file, 'web_image', ['tags' => '']);
        }
    }

    /**
     * @throws Exception
     */
    public function setSmallImageAttribute($file): void
    {
        if ($file) {
            if ($this->media()->where('collection_name', 'mobile_image')->first()) {
                $this->deleteMedia($this->media()->where('collection_name', 'mobile_image')->first()->id);
            }

            $this->addMedia($file, 'mobile_image', ['tags' => '']);
        }
    }

    /**
     * @throws Exception
     */
    public function setDesktopLabelBannerAttribute($file): void
    {
        if ($file) {
            if ($this->media()->where('collection_name', 'desktop_label_banner')->first()) {
                $this->deleteMedia($this->media()->where('collection_name', 'desktop_label_banner')->first()->id);
            }

            $this->addMedia($file, 'desktop_label_banner', ['tags' => '']);
        }
    }

    /**
     * @throws Exception
     */
    public function setMobileLabelBannerAttribute($file): void
    {
        if ($file) {
            if ($this->media()->where('collection_name', 'mobile_label_banner')->first()) {
                $this->deleteMedia($this->media()->where('collection_name', 'mobile_label_banner')->first()->id);
            }

            $this->addMedia($file, 'mobile_label_banner', ['tags' => '']);
        }
    }

    public function getLabelAttribute(): string
    {
        return SliderType::getLabel($this->slider_type);
    }

    public function slider_products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'slider_products', 'slider_id', 'product_id'); // ->where('deleted_at', null);
    }

    protected static function booted(): void
    {
        static::saving(function ($slider) {
            if ($slider->label_name) {
                $slider->label_slug = Str::slug($slider->label_name);
            }
        });
    }
}
