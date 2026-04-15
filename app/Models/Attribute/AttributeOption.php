<?php

namespace App\Models\Attribute;

use App\Models\User\User;
use App\Traits\HasTimezone;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttributeOption extends Model
{
    use HasTimezone;

    protected $connection = 'mysql_internal';

    protected $guarded = [];

    public function attributes(): BelongsTo
    {
        return $this->belongsTo(Attribute::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class, 'attribute_id');
    }

    public function getFormattedOptionAttribute(): array
    {
        $user = $this->user;

        return [
            'id'              => $this->id,
            'attribute_value' => $this->attribute_value,
            'added_by'        => $user ? $user->name : '',
            'created_at'      => Carbon::parse($this->created_at)->format('Y-m-d H:i'),
        ];
    }

    // Append the accessor to include in the JSON response
    protected $appends = ['formatted_option'];
}
