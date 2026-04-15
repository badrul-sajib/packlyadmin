<?php

namespace App\Services\Order\Rules;

use App\Models\Order\Order;
use App\Models\SpamAttempt;
use App\Enums\PaymentMethod;
use App\Enums\SpamOrderStatus;
use function Laravel\Prompts\info;
use Illuminate\Support\Facades\Log;

use App\Models\Merchant\MerchantOrder;
use App\Services\Order\SpamOrderRuleInterface;

class VelocityRule implements SpamOrderRuleInterface
{
    public function check(Order $order): RuleResult
    {
        $ipAddress = request()->ip();

        // Check for multiple failed  attempts
        $failedAttempts = SpamAttempt::where('ip_address', $ipAddress)
            ->whereIn('action_taken',[ SpamOrderStatus::HARD_DECLINE->value, SpamOrderStatus::SOFT_CHALLENGE->value, SpamOrderStatus::HOLD_FOR_REVIEW->value])
            ->where('created_at', '>=', now()->subMinutes(10))
            ->count();
        if ($failedAttempts >= 3) {
            return RuleResult::triggered('Failed multiple attempts', 40);
        }


        if(request()->input('payment_method') === 'COD'){
            // Check for multiple COD orders
            $codOrders = MerchantOrder::query()
                ->whereHas('payment', function ($query) {
                    $query->where('payment_method', PaymentMethod::COD->value);
                })
                ->whereHas('order', function ($query) use ($order) {
                    $query->where('user_id', $order->user_id);
                })
                ->where('created_at', '>=', now()->subHours(24))
                ->distinct('order_id')
                ->count();

            if ($codOrders >= 5) {
                return RuleResult::triggered('Multiple COD orders', 30);
            }
        }else{
            // Check for multiple failed payment attempts from sslcommerz same IP within short time
            $sslOrders = MerchantOrder::query()
                ->whereHas('payment', function ($query) {
                    $query->where('payment_method', PaymentMethod::SSLCOMMERZ->value);
                })
                ->whereHas('order', function ($query) use ($order) {
                    $query->where('user_id', $order->user_id);
                })
                ->where('created_at', '>=', now()->subHours(24))
                ->distinct('order_id')
                ->count();

            if ($sslOrders >= 3) {
                return RuleResult::triggered('Failed Multiple SSLCOMMERZ orders', 30);
            }
        }

        // Check for multiple accounts using same shipping address already have spam order
        // $sameAddressOrders = Order::where('customer_address', $order->customer_address)
        //     ->whereHas('spamAttempt', fn ($query) => $query->where('ip_address', $ipAddress))
        //     ->where('created_at', '>=', now()->subHours(24))
        //     ->count();

        // if ($sameAddressOrders >= 2) {
        //     return RuleResult::triggered('Multiple accounts using same shipping address and same ip address', 25);
        // }

        return RuleResult::notTriggered('no velocity issues detected');
    }

    public function isEnabled(): bool
    {
        return true;
    }
}
