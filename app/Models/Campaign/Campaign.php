<?php

namespace App\Models\Campaign;

use App\Enums\CampaignStatus;
use App\Media\HasMedia;
use App\Models\PrimeView\PrimeView;
use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Campaign extends Model
{
    use HasMedia, HasTimezone, SoftDeletes;

    protected $connection = 'mysql_internal';

    protected $guarded = [];

    protected $appends = ['image', 'logo'];

    protected $casts = [
        'status' => CampaignStatus::class,
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'vendor_request_start' => 'datetime',
        'vendor_request_end' => 'datetime',
    ];

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

    public function setLogoAttribute($file): void
    {
        if ($file) {
            $this->deleteMediaCollection('logo');

            $this->addMedia($file, 'logo');
        }
    }

    public function getLogoAttribute(): string
    {
        return $this->getFirstUrl('logo', config('app.url'));
    }

    public function primeViews(): BelongsToMany
    {
        return $this->belongsToMany(PrimeView::class)
            ->withPivot(['discount_amount', 'discount_type', 'rules'])
            ->withTimestamps();
    }

    public function campaignPrimeViews(): HasMany
    {
        return $this->hasMany(CampaignPrimeView::class);
    }
}
