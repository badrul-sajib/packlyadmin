<?php

namespace App\Models\Notification;

use App\Traits\HasTimezone;
use Illuminate\Notifications\DatabaseNotification;

class Notification extends DatabaseNotification
{
    use HasTimezone;

    protected $connection = 'mysql_internal';

    protected $guarded = [];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $database = config('database.connections.mysql_internal.database');

        $this->table = $database.'.'.'notifications';
    }
}
