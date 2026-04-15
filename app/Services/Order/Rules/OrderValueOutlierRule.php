<?php

namespace App\Services\Order\Rules;

use App\Enums\OrderStatus;
use App\Models\Order\Order;
use App\Models\Merchant\MerchantOrder;
use App\Services\Order\Rules\RuleResult;
use App\Services\Order\SpamOrderRuleInterface;

class OrderValueOutlierRule implements SpamOrderRuleInterface
{
    public function check(Order $order): RuleResult
    {
        if (! $order->user_id) {
            return RuleResult::notTriggered('OrderValueOutlierRule');
        }

        // Check for unusually high order value
        $maxNormalOrder = config('spam.max_normal_order',900000);
        if ($order->total_amount > $maxNormalOrder) {
            return RuleResult::triggered('order value outlier', 20);
        }

        return RuleResult::notTriggered('order value within normal range');
    }

    public function isEnabled(): bool
    {
        return true;
    }
}
