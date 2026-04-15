<?php

namespace App\Services\Order;

use App\Models\Order\Order;
use App\Services\Order\Rules\RuleResult;

interface SpamOrderRuleInterface
{
    public function check(Order $order): RuleResult;

    public function isEnabled(): bool;
}
