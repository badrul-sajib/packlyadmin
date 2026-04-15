<?php

namespace App\Models\Page;

use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Model;

class EPage extends Model
{
    use HasTimezone;

    protected $fillable = [
        'title',
        'label',
        'slug',
        'content',
        'status',
        'serial_no',
        'position',
    ];

    public function setTitleAttribute($value): void
    {
        $this->attributes['title'] = $value;

        if (isset($this->attributes['label']) && $this->attributes['label'] != 4) {
            $this->attributes['slug'] = str($value)->slug();
        }

        $this->attributes['slug'] = str($value)->slug();
    }

    public function setSerialNoAttribute($value): void
    {
        $this->attributes['serial_no'] = $value ?? 0;
        $this->attributes['position']  = 0;
    }
}
