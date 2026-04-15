<?php

namespace App\Models\Payment;

use App\Media\HasMedia;
use App\Media\Mediable;
use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model implements Mediable
{
    use HasMedia, HasTimezone;

    protected $connection = 'mysql_internal';

    protected $guarded = [];

    protected $appends = ['image'];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function setImageAttribute($file): void
    {
        if ($file) {
            $this->deleteMediaCollection('image');

            $this->addMedia($file, 'image');
        }
    }

    public function getImageAttribute(): string
    {
        return $this->getFirstUrl('image', config('app.url'));
    }
}
