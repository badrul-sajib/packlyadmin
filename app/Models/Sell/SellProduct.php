<?php

namespace App\Models\Sell;

use App\Media\HasMedia;
use App\Media\Mediable;
use App\Models\Customer\Customer;
use App\Traits\HasTimezone;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SellProduct extends Model implements Mediable
{
    use HasMedia, HasTimezone;

    protected $connection = 'mysql_internal';

    public $casts = [
        'created_at' => 'datetime:Y-m-d h:i:A',
    ];

    protected $guarded = [];

    public function sell_product_details(): Builder|HasMany|SellProduct
    {
        return $this->hasMany(SellProductDetail::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * @throws Exception
     */
    public function setImageAttribute($file): void
    {
        if ($file) {
            $this->addMedia($file, 'attachment', ['tags' => '']);
        }
    }

    /**
     * @throws Exception
     */
    public function getImageAttribute(): array
    {
        return $this->getUrl('attachment');
    }

    public function sellPayments(): Builder|HasMany|SellProduct
    {
        return $this->hasMany(SellPayment::class);
    }

    public function sellPayment(): Builder|HasOne|SellProduct
    {
        return $this->hasOne(SellPayment::class);
    }
}
