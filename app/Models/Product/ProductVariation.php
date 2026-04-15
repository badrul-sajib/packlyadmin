<?php

namespace App\Models\Product;

use App\Media\HasMedia;
use App\Media\Mediable;
use App\Models\Merchant\Merchant;
use App\Models\Shop\ShopProductVariation;
use App\Models\Stock\StockInventory;
use App\Models\Variation\VariationAttribute;
use App\Traits\HasTimezone;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ProductVariation extends Model implements Mediable
{
    use HasMedia, HasTimezone;

    protected $connection = 'mysql_internal';

    protected $guarded = [];

    protected $appends = ['image'];

    protected $hidden = ['media'];

    public function stockInventory(): HasOne
    {
        return $this->hasOne(StockInventory::class);
    }

    public function variationAttributes(): HasMany
    {
        return $this->hasMany(VariationAttribute::class);
    }

    public function variations(): HasMany
    {
        return $this->hasMany(VariationAttribute::class);
    }

    /**
     * @throws Exception
     */
    public function setImageAttribute($file): void
    {
        if ($file) {
            $this->deleteMedia();
            $this->addMedia($file, 'image', ['tags' => '']);
        }
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getImageAttribute(): string
    {
        return $this->getFirstUrl('image', config('app.url'));
    }

    public static function generateUnique12DigitBarcode(): string
    {
        do {
            $barcode = (string) mt_rand(100000000000, 999999999999);
        } while (self::where('barcode', $barcode)->exists());

        return $barcode;
    }

    public function shopVariation(): HasOne
    {
        return $this->hasOne(ShopProductVariation::class);
    }

    public function stockInventories(): Builder|HasMany|ProductVariation
    {
        return $this->hasMany(StockInventory::class);
    }

    public function merchant()
    {
        return $this->hasOneThrough(
            Merchant::class,
            Product::class,
            'id',
            'id',
            'product_id',
            'merchant_id'
        );
    }
}
