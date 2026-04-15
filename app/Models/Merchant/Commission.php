<?php

namespace App\Models\Merchant;

use App\Models\Category\Category;
use App\Models\Product\Product;
use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Commission extends Model
{
    use HasTimezone,LogsActivity;

    protected $guarded = [];

    protected $connection = 'mysql_internal';

    protected static array $logAttributes = ['commission_rate', 'category_id', 'merchant_id', 'product_id', 'start_date', 'end_date'];

    protected static array $logAttributesToIgnore = [];

    protected static bool $logOnlyDirty = true;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['commission_rate', 'category_id', 'merchant_id', 'product_id', 'start_date', 'end_date']);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }
}
