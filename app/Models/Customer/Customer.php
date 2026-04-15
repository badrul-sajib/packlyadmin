<?php

namespace App\Models\Customer;

use App\Enums\CustomerTypes;
use App\Media\HasMedia;
use App\Media\Mediable;
use App\Models\Merchant\Merchant;
use App\Traits\HasTimezone;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Customer extends Model implements Mediable
{
    use HasMedia, HasTimezone;

    protected $connection = 'mysql_internal';

    protected $guarded = [];

    protected $appends = ['image', 'customer_type_label'];

    protected $casts = [
        'customer_type_id' => CustomerTypes::class,
    ];

    /**
     * @throws Exception
     */
    public function setImageAttribute($file): void
    {
        if ($file) {
            $this->addMedia($file, 'images', ['tags' => '']);
        }
    }

    /**
     * @throws Exception
     */
    public function getImageAttribute()
    {
        return $this->getUrl('images', env('APP_URL'))[0] ?? null;
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }
    public function getCustomerTypeLabelAttribute(): ?string
    {
        return $this->customer_type_id?->getValues();
    }
}
