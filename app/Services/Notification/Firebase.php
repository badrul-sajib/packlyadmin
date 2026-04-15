<?php

namespace App\Services\Notification;

class Firebase
{
    public static function send($heading, $message, $deviceIds, $data = []): bool
    {
        // Static mode: Firebase push notifications are disabled
        return true;
    }
}
