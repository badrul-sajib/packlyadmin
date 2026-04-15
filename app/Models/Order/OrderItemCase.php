<?php

namespace App\Models\Order;

use App\Enums\ItemStatus;
use App\Media\HasMedia;
use App\Media\Mediable;
use App\Traits\HasTimezone;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItemCase extends Model implements Mediable
{
    use HasMedia, HasTimezone;

    protected $connection = 'mysql_internal';

    protected $guarded = [];

    protected $appends = ['images', 'status_label'];

    protected $hidden = ['media', 'images'];

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function reason(): BelongsTo
    {
        return $this->belongsTo(Reason::class);
    }

    /**
     * @throws Exception
     */
    public function setImagesAttribute($files): void
    {
        if ($files) {
            foreach ($files as $file) {
                $this->addMedia($file, 'images');
            }
        }
    }

    public function getImagesAttribute(): array
    {
        return $this->getMedia('images');
    }

    public function getStatusLabelAttribute(): string
    {
        return ItemStatus::getLabel()[$this->status] ?? 'Unknown';
    }
}
