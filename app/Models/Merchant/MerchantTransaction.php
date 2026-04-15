<?php

namespace App\Models\Merchant;

use App\Media\HasMedia;
use App\Media\Mediable;
use App\Models\Account\Account;
use App\Traits\HasTimezone;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MerchantTransaction extends Model implements Mediable
{
    use HasMedia,HasTimezone;

    protected $connection = 'mysql_internal';

    protected $guarded = [];

    /**
     * @throws Exception
     */
    public function setAttachmentAttribute($file): void
    {
        if ($file) {
            $this->deleteMedia();

            $this->addMedia($file, 'merchant_transaction', ['tags' => '']);
        }
    }

    public function getAttachmentAttribute(): string
    {
        return $this->getFirstUrl('merchant_transaction');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}
