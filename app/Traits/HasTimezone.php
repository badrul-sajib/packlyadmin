<?php

namespace App\Traits;

trait HasTimezone
{
    protected function asDateTime($value)
    {
        $date = parent::asDateTime($value);

        return $date->tz(config('app.timezone'));
    }
}
