<?php

namespace App\Models\Product;

use App\Enums\BadgeType;
use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\Activitylog\Models\Activity;

class Badge extends Model
{
    use HasTimezone;

    protected $connection = 'mysql_internal';

    protected $fillable = [
        'name',
        'type',
        'status',
    ];

    public array $cast = [
        'type' => BadgeType::class,
    ];

    public function activities(): MorphMany
    {
        return $this->morphMany(Activity::class, 'subject');
    }

    protected $appends = ['type_label'];

    public function status_label(): string
    {
        return $this->status == '1' ? 'Active' : 'Inactive';
    }

    public function badge_products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'badge_products', 'badge_id', 'product_id');
    }

     public function products()
    {
        return $this->belongsToMany(Product::class, 'badge_products');
    }

    public function badgeProducts()
    {
        return $this->hasMany(BadgeProduct::class);
    }

    public function getTypeLabelAttribute($value): string
    {
        return BadgeType::toArray()[$this->type];
    }
}
