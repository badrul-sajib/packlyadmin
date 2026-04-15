<?php

namespace App\Models\Review;

use App\Media\HasMedia;
use App\Media\Mediable;
use App\Models\Order\OrderItem;
use App\Models\Product\Product;
use App\Models\User\User;
use App\Traits\HasTimezone;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model implements Mediable
{
    use HasMedia, HasTimezone;

    protected $connection = 'mysql_internal';

    protected $guarded = ['id'];

    protected $appends = ['images'];

    protected $hidden = ['media'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id');
    }

    /**
     * @throws Exception
     */
    public function getImagesAttribute(): array
    {
        return $this->getUrl('images', env('APP_URL'));
    }
}
