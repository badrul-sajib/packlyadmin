<?php

namespace App\Models\Merchant;

use App\Media\HasMedia;
use App\Media\Mediable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MerchantIssueType extends Model implements Mediable
{
    use HasFactory, HasMedia;

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    public function setAttachmentAttribute($file): void
    {
        if ($file) {
            $this->addMedia($file, 'attachments', ['tags' => '']);
        }
    }

    /**
     * @throws Exception
     */
    public function getAttachmentAttribute(): array
    {

        return $this->getUrl('attachments', config('app.url'));
    }

    public function issues(): HasMany
    {
        return $this->hasMany(MerchantIssue::class, 'merchant_issue_type_id');
    }
}
