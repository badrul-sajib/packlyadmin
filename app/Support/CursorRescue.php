<?php 


namespace App\Support;

use Closure;
use Exception;
use Illuminate\Support\Facades\Log;

class CursorRescue
{
    public static function run(Closure $callback)
    {
        try {
            return $callback();
        } catch (\Throwable $th) {
            Log::error('Cursor error: '.$th->getMessage());
            throw new Exception('Invalid cursor provided');
        }
    }
}
