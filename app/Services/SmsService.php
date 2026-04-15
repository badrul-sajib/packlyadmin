<?php

namespace App\Services;

class SmsService
{
    public function sendMessage($phoneNumber, $messageContent): array|string
    {
        // Static mode: SMS sending is disabled
        return ['status' => 'ok', 'message' => 'SMS disabled in static mode.'];
    }
}
