<?php

namespace App\Traits;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait Transaction
{
    /**
     * Execute a callback within a database transaction, with rollback functionality.
     *
     * @return mixed
     *
     * @throws \Throwable
     */
    public static function rollback(\Closure $callback)
    {
        return DB::transaction(function () use ($callback) {
            return $callback();
        });
    }

    /**
     * Execute a callback within a database transaction, with retry and rollback functionality.
     *
     * @return mixed
     *
     * @throws \Throwable
     */
    public static function retryAndRollback(\Closure $callback)
    {
        try {
            // Retry up to 3 times, waiting 100ms between attempts
            return DB::transaction(function () use ($callback) {
                return retry(3, function () use ($callback) {
                    return $callback();
                }, 100);
            });
        } catch (Exception $e) {
            // Log the exception for debugging purposes
            Log::error('Transaction failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }
}
