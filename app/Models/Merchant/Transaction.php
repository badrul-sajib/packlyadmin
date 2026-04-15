<?php

namespace App\Models\Merchant;

use App\Media\HasMedia;
use App\Media\Mediable;
use App\Models\Wallet\Wallet;
use App\Traits\HasTimezone;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model implements Mediable
{
    use HasMedia, HasTimezone;

    protected $connection = 'mysql_internal';

    protected $appends = ['formatted_transaction_type', 'attachment'];

    public static string $TYPE_WITHDRAW = 'withdraw';

    public static string $TYPE_DEPOSIT = 'deposit';

    protected $guarded = [];

    protected $casts = [
        'created_at' => 'datetime:d M Y h:i A',
    ];

    public static function boot(): void
    {
        parent::boot();

        static::creating(function ($transaction) {
            $transaction->transaction_id = self::generateTransactionId(auth()->user()->merchant->id);
        });
    }

    public static function generateTransactionId($merchantId): string
    {
        $lastTransaction = self::where('merchant_id', $merchantId)
            ->latest('id')
            ->value('transaction_id');

        $nextNumber = $lastTransaction
            ? (int) substr($lastTransaction, 3) + 1
            : 1;

        return 'SFC'.str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    /**
     * @throws Exception
     */
    public function setAttachmentAttribute($file): void
    {
        if ($file) {
            $this->deleteMedia();

            $this->addMedia($file, 'transaction', ['tags' => '']);
        }
    }

    public function getAttachmentAttribute(): string
    {
        return $this->getFirstUrl('transaction');
    }

    public function getFormattedTransactionTypeAttribute(): string
    {
        return ucwords(str_replace('-', ' ', $this->transaction_type));
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }
}
