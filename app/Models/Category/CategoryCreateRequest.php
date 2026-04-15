<?php

namespace App\Models\Category;

use App\Models\Merchant\Merchant;
use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CategoryCreateRequest extends Model
{
    use HasTimezone;

    protected $connection = 'mysql_internal';

    public $casts = [
        'created_at' => 'datetime:Y-m-d H:i:A',
        'updated_at' => 'datetime:Y-m-d H:i:A',
        'data'       => 'array',
    ];

    protected $guarded = [];

    public static string $PENDING = '1';

    public static string $APPROVED = '2';

    public static string $REJECTED = '3';

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }
}
