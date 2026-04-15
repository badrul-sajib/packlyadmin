<?php

namespace App\Jobs;

use App\Services\SmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendSMS implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The phone number (E.164 format recommended).
     */
    public string $to;

    /**
     * The message body.
     */
    public string $message;

    /**
     * Create a new job instance.
     *
     * @param  string  $to  Customer phone number
     * @param  string  $message  Notification text
     */
    public function __construct(string $to, string $message)
    {
        $this->to      = $to;
        $this->message = $message;
    }

    /**
     * Execute the job.
     */
    public function handle(SmsService $smsService): void
    {
        try {
            $smsService->sendMessage($this->to, $this->message);
        } catch (Throwable $e) {
            // Log the failure – the job will be retried according to your queue config
            Log::error('SMS send failed', [
                'to'        => $this->to,
                'message'   => $this->message,
                'exception' => $e->getMessage(),
            ]);

            // Re-throw so Laravel's queue system can handle retries / failures
            throw $e;
        }
    }
}
