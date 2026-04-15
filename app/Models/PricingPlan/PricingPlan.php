<?php

namespace App\Models\PricingPlan;

use App\Enums\RecurringTypes;
use Illuminate\Database\Eloquent\Model;

class PricingPlan extends Model
{
    protected $table = 'pricing_plans';

    protected $fillable = [
        'name',
        'description',
        'price',
        'recurring_type',
        'modules',
        'status',
    ];

    protected $casts = [
        'modules' => 'array',
        'recurring_type' => 'integer',
        'price' => 'decimal:2',
        'status' => 'boolean',
    ];

    public function getRecurringTypeLabelAttribute(): string
    {
        return RecurringTypes::from($this->recurring_type)->label();
    }
}
