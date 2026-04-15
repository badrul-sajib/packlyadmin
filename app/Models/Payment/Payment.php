<?php

namespace App\Models\Payment;

use App\Media\HasMedia;
use App\Media\Mediable;
use App\Models\Purchase\Purchase;
use App\Traits\HasTimezone;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model implements Mediable
{
    use HasMedia, HasTimezone;

    protected $connection = 'mysql_internal';

    protected $guarded = [];

    protected $appends = ['attachment'];

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }

    /**
     * @throws Exception
     */
    public function setAttachmentAttribute($file): void
    {
        if ($file) {
            $this->deleteMedia();

            $this->addMedia($file, 'attachment', [
                'tags' => 'featured,thumbnail',
            ]);
        }
    }

    public function getAttachmentAttribute(): string
    {
        return $this->getFirstUrl('attachment');
    }
}
