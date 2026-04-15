<?php

namespace App\Models\Campaign;

use App\Enums\CampaignProductStatus;
use App\Models\Merchant\Merchant;
use App\Models\PrimeView\PrimeView;
use App\Models\Product\Product;
use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignProduct extends Model
{
    use HasTimezone;

    protected $connection = 'mysql_internal';

    protected $guarded = [];

    protected $casts = [
        'status' => CampaignProductStatus::class,
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class)->withTrashed();
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function primeView(): BelongsTo
    {
        return $this->belongsTo(PrimeView::class);
    }
}
