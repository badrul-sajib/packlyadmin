<?php

namespace App\Jobs;

use App\Models\Order\Order;
use App\Services\Order\SpamOrderService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessSpamCheck implements ShouldQueue
{
    use Queueable;

    public $orderId;

    public $spamOrderService;

    public function __construct($orderId)
    {
        $this->orderId = $orderId;
        $this->spamOrderService = new SpamOrderService;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $order = Order::find($this->orderId);
        if (! $order) {
            return;
        }

        $this->spamOrderService->checkOrder($order);
    }
}
