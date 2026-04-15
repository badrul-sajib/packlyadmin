<?php

namespace App\Models\Supplier;

use App\Media\HasMedia;
use App\Media\Mediable;
use App\Models\Merchant\Merchant;
use App\Models\Purchase\Purchase;
use App\Traits\HasTimezone;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model implements Mediable
{
    use HasMedia, HasTimezone;

    protected $connection = 'mysql_internal';

    protected $guarded = [];

    protected $appends = ['image'];

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    /**
     * @throws Exception
     */
    public function setImageAttribute($file): void
    {
        if ($file) {
            $this->deleteMedia();

            $this->addMedia($file, 'images', ['tags' => '']);
        }
    }

    public function getImageAttribute(): string
    {
        return $this->getFirstUrl('images');
    }

    public function purchases(): Builder|HasMany|Supplier
    {
        return $this->hasMany(Purchase::class);
    }

    public function payments(): Builder|HasMany|Supplier
    {
        return $this->hasMany(SupplierPayment::class, 'supplier_id');
    }
}
