<?php

namespace App\Services;

use App\Models\ErrorLog\ErrorLog;

class ErrorLogService
{
    public static function log(array $data)
    {
        $signature = self::generateSignature($data);

        $log = ErrorLog::where('signature', $signature)->first();

        if ($log) {
            $log->increment('occurrence_count');
            $log->update($data);
            $log->update(['last_occurred_at' => now()]);

            return $log;
        }

        if (isset($data['viewport']) && is_array($data['viewport'])) {
            $data['viewport'] = json_encode($data['viewport']);
        }

        $log = ErrorLog::create(array_merge($data, [
            'signature'        => $signature,
            'last_occurred_at' => now(),
        ]));

        return $log;
    }

    protected static function generateSignature(array $data): string
    {
        if ($data['source'] === 'backend') {
            return md5(
                ($data['message'] ?? '').
                ($data['file'] ?? '').
                ($data['line'] ?? '').
                ($data['client_type'] ?? '').
                ($data['environment'] ?? '')
            );
        }

        return md5(
            ($data['status_code'] ?? '').
            ($data['endpoint'] ?? '').
            ($data['current_route'] ?? '').
            ($data['user_agent'] ?? '').
            ($data['environment'] ?? '').
            ($data['message'] ?? '')
        );
    }
}
