<?php

namespace App\Models\Promotion;

use App\Media\HasMedia;
use App\Media\Mediable;
use App\Traits\HasTimezone;
use Exception;
use Illuminate\Database\Eloquent\Model;

class PromotionAndService extends Model implements Mediable
{
    use HasMedia, HasTimezone;

    protected $connection = 'mysql_internal';

    protected $fillable = [
        'title',
        'description',
        'status',
        'image',
        'type',
    ];

    protected $hidden = ['media'];

    /**
     * @throws Exception
     */
    public function setImageAttribute($file): void
    {
        if ($file) {
            if ($this->media()->where('collection_name', 'images')->first()) {
                $this->deleteMedia($this->media()->where('collection_name', 'images')->first()->id);
            }
            $this->addMedia($file, 'images', ['tags' => '']);
        }
    }

    public function getImageAttribute(): string
    {
        return $this->getFirstUrl('images', env('APP_URL'));
    }
}
