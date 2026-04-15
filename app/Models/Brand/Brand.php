<?php

namespace App\Models\Brand;

use App\Media\HasMedia;
use App\Media\Mediable;
use App\Models\Merchant\Merchant;
use App\Models\Product\Product;
use App\Traits\HasTimezone;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Brand extends Model implements Mediable
{
    use HasMedia, HasTimezone;

    protected $connection = 'mysql_internal';

    protected $guarded = [];

    protected $appends = ['image'];

    public function scopeActive($query)
    {
        return $query->where('status', 1);
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

    public function products(): Brand|Builder|HasMany
    {
        return $this->hasMany(Product::class);
    }
    public function merchants(): HasOne|Brand|Builder
    {
        return $this->hasOne(Merchant::class, 'id', 'merchant_id');
    }

}
