<?php

namespace App\Services\Order;

use App\Enums\SpamOrderStatus;
use App\Models\Order\Order;
use App\Models\SpamAttempt;
use App\Services\Order\Rules\AddressValidationRule;
use App\Services\Order\Rules\CancelledAbuseRule;
use App\Services\Order\Rules\OrderValueOutlierRule;
use App\Services\Order\Rules\RefundAbuseRule;
use App\Services\Order\Rules\VelocityRule;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class SpamOrderService
{
    public function getSpamOrders($request): LengthAwarePaginator
    {
        $perPage = $request->input('perPage', 10);
        $page = $request->input('page', 1);
        $search = $request->input('search', '');
        $shipping_type = $request->input('ship_type', '');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        return Order::query()
            ->isSpam()
            ->with([
                'spamAttempt',
                'merchantOrders:id,order_id',
                'merchantOrders.payment:id,merchant_order_id,payment_method',
            ])
            ->withCount('orderItems')
            ->when($search, function ($query) use ($search) {
                $query->whereAny(['invoice_id', 'customer_number'], 'like', "%{$search}%");
            })
            ->when($shipping_type, function ($query) use ($shipping_type) {
                $query->where('shipping_type', $shipping_type);
            })
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate.' 00:00:00', $endDate.' 23:59:59']);
            })
            ->latest()
            ->paginate($perPage, ['*'], 'page', $page);
    }

    public function checkOrder(Order $order): ?SpamAttempt
    {
        try {

            $rules = [
                new VelocityRule,
                new OrderValueOutlierRule,
                new CancelledAbuseRule,
            ];


            $fraudScore = 0;
            $triggeredRules = [];
            $triggeredChecks = [];
            $ipAddress = request()->ip();

            // validate rules
            if (empty($rules)) {
                throw new \Exception('No rules found');
            }

            // Run all enabled rules
            foreach ($rules as $rule) {
                // if ($rule->isEnabled()) {
                    $result = $rule->check($order);

                    if ($result->isTriggered()) {
                        $fraudScore += $result->getScore();
                        $triggeredRules[] = $result->getRuleName();
                        if ($result->getTriggeredChecks()) {
                            $triggeredChecks[] = $result->getTriggeredChecks();
                        }
                    }
                // }
            }

            // Determine action based on fraud score
            $action = $this->determineAction($fraudScore, $triggeredRules);

            if ($action == SpamOrderStatus::ALLOW->value) {
                return null;
            }
            // Save spam check results SpamAttempt::create(
            $spamAttempt = SpamAttempt::create([
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'ip_address' => $ipAddress,
                'fraud_score' => max(0, min(100, $fraudScore)),
                'triggered_rules' => json_encode($triggeredRules),
                'action_taken' => $action,
                'status' => '1',
                'metadata' => json_encode($triggeredChecks),
            ]);

            $order->update([
                'is_spam' => true,
            ]);

            return $spamAttempt;
        } catch (\Throwable $th) {
            // Log error
            Log::error('SpamOrderService Error: '.$th->getMessage());

            return null;
        }
    }

    protected function determineAction(int $fraudScore, array $triggeredRules): string
    {
        // Rule-based actions (higher priority)
        if (in_array('CancelledAbuseRule', $triggeredRules)) {
            return 'HARD_DECLINE';
        }
        // Score-based actions
        if ($fraudScore >= 80) {
            return 'HARD_DECLINE';
        } elseif ($fraudScore >= 50) {
            return 'HOLD_FOR_REVIEW';
        } elseif ($fraudScore >= 20) {
            return 'SOFT_CHALLENGE';
        }

        return 'ALLOW';
    }
}
