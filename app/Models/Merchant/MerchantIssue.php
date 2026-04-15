<?php

namespace App\Models\Merchant;

use App\Enums\MerchantIssueStatus;
use App\Media\HasMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MerchantIssue extends Model
{
    use HasFactory, HasMedia;

    protected $fillable = [
        'merchant_id',
        'merchant_issue_type_id',
        'message',
        'status',
    ];

    protected $casts = [
        'status' => 'integer',
    ];

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(MerchantIssueType::class, 'merchant_issue_type_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return MerchantIssueStatus::labels()[$this->status] ?? 'pending';
    }
}
