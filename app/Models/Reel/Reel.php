<?php

namespace App\Models\Reel;

use App\Media\HasMedia;
use App\Media\Mediable;
use App\Models\Merchant\Merchant;
use App\Models\Merchant\MerchantSetting;
use App\Models\Product\Product;
use App\Traits\HasTimezone;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reel extends Model implements Mediable
{
    use HasMedia, HasTimezone;

    protected $connection = 'mysql_internal';

    protected $guarded = [];

    protected $appends = ['image','video','thumbnail_image'];

    protected $hidden = ['media'];

    /**
     * @throws Exception
     */
    public function setImageAttribute($file): void
    {
        if ($file) {
            $this->deleteMediaCollection('image');
            $this->addMedia($file, 'image');
        }
    }
    public function setVideoAttribute($file): void
    {
        if ($file) {
            $this->deleteMediaCollection('video');
            $this->addMedia($file, 'video');
        }
    }
    public function setThumbnailImageAttribute($file): void
    {
        if ($file) {
            $this->deleteMediaCollection('thumbnail_image');
            $this->addMedia($file, 'thumbnail_image');
        }
    }

    public function getThumbnailImageAttribute(): string
    {
        return $this->getFirstUrl('thumbnail_image', config('app.url'));
    }

    public function getImageAttribute(): string
    {
        return $this->getFirstUrl('image', config('app.url'));
    }
    public function getVideoAttribute(): string
    {
        return $this->getFirstUrl('video', config('app.url'));
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Merchant::class, 'merchant_id', 'id');
    }

    public function getShopLogoAttribute()
    {
        $setting = MerchantSetting::where('merchant_id', $this->shop?->id)
            ->where('key', 'shop_settings')
            ->first();

        if (! $setting || ! $setting->value) {
            return null;
        }

        $value = json_decode($setting->value, true);

        return $value['shop_logo_and_cover']['shop_logo']['image'] ?? null;
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class, 'merchant_id', 'id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function reelUserActions(): Builder|HasMany|Reel
    {
        return $this->hasMany(ReelUserAction::class);
    }
}
