<?php

namespace App\Services\Order\Rules;

use App\Enums\OrderStatus;
use App\Models\Order\Order;
use App\Models\Merchant\MerchantOrder;
use App\Services\Order\Rules\RuleResult;
use App\Services\Order\SpamOrderRuleInterface;

class RefundAbuseRule  implements SpamOrderRuleInterface
{
    public function check(Order $order): RuleResult
    {
        if (! $order->user_id) {
            return RuleResult::notTriggered('refund abuse rule');
        }

        // Check refunded rate
        $totalOrders = MerchantOrder::query()
            ->whereHas('order', function ($query) use ($order) {
                $query->where('user_id', $order->user_id);
            })->count();

        $refundedOrders = MerchantOrder::query()
            ->whereHas('order', function ($query) use ($order) {
                $query->where('user_id', $order->user_id);
            })
            ->where('status_id', OrderStatus::REFUNDED->value)->count();

        if ($totalOrders >= 5 && ($refundedOrders / $totalOrders) > 0.5) {
            return RuleResult::triggered('refund abuse', 60);
        }

        // Check recent refunded
        $recentRefunded = MerchantOrder::query()
            ->whereHas('order', function ($query) use ($order) {
                $query->where('user_id', $order->user_id);
            })
            ->where('status_id', OrderStatus::REFUNDED->value)
            ->where('created_at', '>=', now()->subDays(30))->count();

        if ($recentRefunded >= 2) {
            return RuleResult::triggered('refund abuse', 50);
        }

        return RuleResult::notTriggered('refund abuse');
    }

    public function isEnabled(): bool
    {
        return config('spam.rules.refund_abuse.enabled', true);
    }
}
