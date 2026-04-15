<?php

namespace App\Media;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    protected $connection = 'mysql_internal';

    public static $disk_name = 'public';

    protected $appends = ['full_url'];

    protected $fillable = [
        'model_type',
        'model_id',
        'collection_name',
        'file_path',
        'file_type',
        'tags',
    ];

    public function __construct()
    {
        self::$disk_name = config('filesystems.default') ?? self::$disk_name;
    }

    public function getFullUrlAttribute()
    {
        return Storage::disk(Media::$disk_name)->url($this->attributes['file_path']);
    }

    /**
     * Get the owning model of the media (polymorphic relationship).
     */
    public function mediable()
    {
        return $this->morphTo();
    }

    protected static function booted()
    {
        static::deleting(function ($media) {
            Storage::disk(Media::$disk_name)->delete($media->file_path);
        });
    }
}
