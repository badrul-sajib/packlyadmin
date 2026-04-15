<?php

namespace App\Services\Order\Rules;

use App\Enums\OrderStatus;
use App\Models\Order\Order;
use App\Models\Merchant\MerchantOrder;
use App\Services\Order\Rules\RuleResult;
use App\Services\Order\SpamOrderRuleInterface;

class CancelledAbuseRule implements SpamOrderRuleInterface
{
    public function check(Order $order): RuleResult
    {

        // Check cancelled rate
        $totalOrders = MerchantOrder::query()
            ->whereHas('order', function ($query) use ($order) {
                $query->where('user_id', $order->user_id);
            })->count();


        $cancelledOrders = MerchantOrder::query()
            ->whereHas('order', function ($query) use ($order) {
                $query->where('user_id', $order->user_id);
            })
            ->where('status_id', OrderStatus::CANCELLED->value)->count();


        if ($totalOrders >= 5 && ($cancelledOrders / $totalOrders) > 0.5) {
            return RuleResult::triggered('cancelled abuse ', 60);
        }

        // Check recent cancelled
        $recentCancelled = MerchantOrder::query()
            ->whereHas('order', function ($query) use ($order) {
                $query->where('user_id', $order->user_id);
            })
            ->where('status_id', OrderStatus::CANCELLED->value)
            ->where('created_at', '>=', now()->subDays(30))->count();

        if ($recentCancelled >= 3) {
            return RuleResult::triggered('cancelled abuse', 50);
        }

        return RuleResult::notTriggered('CancelledAbuseRule');
    }

    public function isEnabled(): bool
    {
        return config('spam.rules.cancelled_abuse.enabled', true);
    }
}
