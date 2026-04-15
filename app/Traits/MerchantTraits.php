<?php

namespace App\Traits;

use App\Enums\OrderStatus;
use App\Enums\PayoutRequestStatus;
use App\Enums\MerchantStatus;
use App\Models\Payout\Payout;
use App\Models\Merchant\Merchant;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

trait MerchantTraits
{
    public function merchantList($request): LengthAwarePaginator|array
    {
        $perPage                = $request->input('perPage', 10);
        $page                   = $request->input('page', 1);
        $search                 = $request->input('search', '');
        $search_type            = $request->input('search_type', 'id');
        $shop_status            = $request->input('shop_status', '');
        $shop_type              = $request->input('shop_type', '');
        $shop_verification      = $request->input('shop_verification', null);
        $adminId                = $request->input('admin_id', null);

        $merchant = Merchant::query()
            ->when($search && $search_type, function ($query) use ($search, $search_type) {
                if ($search_type === 'id') {
                    $query->where('id', $search); // exact match for ID
                } elseif ($search_type === 'phone') {
                    $query->where('phone', 'like', '%'.$search.'%');
                } elseif ($search_type === 'name') {
                    $query->where('name', 'like', '%'.$search.'%');
                }
            })
            ->when($search && ! $search_type, function ($query) use ($search) {
                // fallback if no search_type selected
                $query->whereAny(['phone', 'name', 'shop_name'], 'like', '%'.$search.'%');
            })
            ->when($shop_status != '', function ($query) use ($shop_status) {
                $query->where('shop_status', $shop_status);
            })
            ->when($shop_verification == '0' || $shop_verification, function ($query) use ($shop_verification) {
                $query->where('is_verified', $shop_verification);
            })
            ->when($shop_type != '', function ($query) use ($shop_type) {
                if ($shop_type == 'online') {
                    $query->where('shop_status', MerchantStatus::Active->value);
                }
                if ($shop_type == 'offline') {
                    $query->where('shop_status', '!=', MerchantStatus::Active->value);
                }
            })
            ->when($adminId,function($query) use ($adminId){
                $query->where('admin_id', $adminId);
            })
            ->with(['orders.orderItems' => function ($query) {
                $query->selectRaw('merchant_order_id, SUM(price * quantity) as total_price, commission as total_commission')
                    ->where('status_id', OrderStatus::DELIVERED->value)
                    ->groupBy('merchant_order_id');
            }])
            ->latest()
            ->paginate($perPage, ['*'], 'page', $page);

        $merchant->getCollection()->transform(function ($merchant) {
            $totalPrice      = 0;
            $totalCommission = 0;

            $payoutAmount = Payout::where('merchant_id', $merchant->id)
                ->whereIn('status', [PayoutRequestStatus::APPROVED->value, PayoutRequestStatus::PENDING->value])
                ->sum('amount');

            foreach ($merchant->orders as $order) {
                foreach ($order->orderItems as $item) {
                    $totalPrice      += $item->total_price      ?? 0;
                    $totalCommission += $item->total_commission ?? 0;
                }
            }

            $merchant->total_sell       = $totalPrice;
            $merchant->packly_earning   = $totalCommission;
            $merchant->merchant_earning = $totalPrice                      - $totalCommission;
            $merchant->current_balance  = ($totalPrice - $totalCommission) - $payoutAmount;
            $merchant->total_payout     = $payoutAmount;

            return $merchant;
        });

        return $merchant;
    }
}
