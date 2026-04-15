<?php

namespace App\Actions;

use App\Enums\OrderStatus;
use App\Models\Merchant\Merchant;
use App\Models\Merchant\MerchantOrder;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class FetchMerchantOrders
{
    public function execute($request): LengthAwarePaginator
    {
        $perPage = $request->input('perPage', 10);
        $page = $request->input('page', 1);
        $search = $request->input('search');
        $merchantId = $request->input('merchant_id');
        $status = $request->input('status');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $adminId = $request->input('admin_id');
        $cnId = $request->input('cn_id');

        $merchantIds = [];
        if ($adminId) {
            $merchantIds = Merchant::where('admin_id', $adminId)->pluck('id')->toArray();
        }

        $query = MerchantOrder::query()
            ->whereHas('order', fn($q) => $q->notSpam())
            ->with(
                'merchant:id,name,shop_name,phone',
                'order:id,invoice_id,shipping_type,customer_address'
            )
            ->withCount('orderItems');

        if ($search || $cnId) {
            $query->when(
                $cnId,
                fn($q) => $q->where('consignment_id', operator: $cnId)
            );

            $query->when($search, function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('tracking_id', $search)
                        ->orWhere('invoice_id', $search)
                        ->orWhereHas(
                            'merchant',
                            fn($q) => $q->whereAny(['name', 'phone', 'shop_name'], 'like', "%{$search}%")
                        )
                        ->orWhereHas(
                            'order',
                            fn($q) => $q->where('invoice_id', $search)
                        );
                });
            });
        } else {
            $query->when(
                $merchantId,
                fn($q) => $q->where('merchant_id', $merchantId)
            );

            $query->when(
                $status,
                fn($q) => $q->where('status_id', $status)
            );

            $query->when(
                $merchantIds,
                fn($q) => $q->whereIn('merchant_id', $merchantIds)
            );

            $query->when(
                $startDate && $endDate,
                fn($q) => $q->whereBetween('created_at', [
                    Carbon::parse($startDate)->startOfDay(),
                    Carbon::parse($endDate)->endOfDay(),
                ])
            );
        }

        return $query
            ->latest()
            ->paginate($perPage, ['*'], 'page', $page);
    }

    public function payoutOrders($request, ?Merchant $merchant = null): Collection
    {
        $search = $request->input('search');
        $merchantId = $request->input('merchant_id');

        $days = 0;
        if ($merchant) {
            $shopSettings = $merchant->getShopSettings();
            $days = (int) $shopSettings['payout_request_date'];
        }

        $query = MerchantOrder::query()
            ->where('status_id', OrderStatus::DELIVERED->value)
            ->whereNull('payout_id')
            ->when($merchantId, fn($q) => $q->where('merchant_id', $merchantId))
            ->whereHas('order', fn($q) => $q->notSpam())
            ->with([
                'merchant:id,name,shop_name,phone',
                'order:id,invoice_id',
            ])
            ->withCount('orderItems');

        // Apply eligibility filter: only orders delivered at least $days days ago
        if ($days > 0) {
            $query->where(function ($q) use ($days) {
                $q->where(function ($q) use ($days) {
                    $q->whereNotNull('delivered_at')
                        ->whereDate('delivered_at', '<=', now()->subDays($days));
                })->orWhere(function ($q) use ($days) {
                    $q->whereNull('delivered_at')
                        ->whereDate('updated_at', '<=', now()->subDays($days));
                });
            });
        }

        $query->when($search, function ($q) use ($search) {
            $q->where(function ($q) use ($search) {
                $q->where('tracking_id', 'like', "%{$search}%")
                    ->orWhere('invoice_id', 'like', "%{$search}%")
                    ->orWhere('consignment_id', 'like', "%{$search}%")
                    ->orWhereHas(
                        'order',
                        fn($q) => $q->where('invoice_id', 'like', "%{$search}%")
                    );
            });
        });

        return $query->latest()->get();
    }


    public function executeMerchantOrders($request): LengthAwarePaginator
    {
        $perPage = $request->input('perPage', 10);
        $page = $request->input('page', 1);
        $search = $request->input('search', '');
        $merchant_id = $request->input('merchant_id', '');
        $status = $request->input('status', '');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $adminId = $request->input('admin_id');
        $cnId = $request->input('cn_id');

        $merchantIds = [];
        if ($adminId) {
            $merchantIds = Merchant::where('admin_id', $adminId)->pluck('id')->toArray();
        }

        return MerchantOrder::query()
            ->whereHas('order', function ($query) {
                $query->notSpam();
            })
            ->with('merchant:id,name,shop_name,phone', 'order:id,invoice_id,shipping_type,customer_address')
            ->withCount('orderItems')
            ->when($merchant_id, function ($query, $merchant_id) {
                return $query->where('merchant_id', $merchant_id);
            })
            ->when($search, function ($query, $search) {
                return $query->where(function ($query) use ($search) {
                    $query->where('tracking_id', $search)->orWhere('invoice_id', $search)
                        ->orWhereHas('merchant', function ($query) use ($search) {
                            $query->whereAny(['name', 'phone', 'shop_name'], 'like', "%{$search}%");
                        })
                        ->orWhereHas('order', function ($query) use ($search) {
                            $query->where('invoice_id', $search);
                        });
                });
            })
            ->when($cnId, function ($query, $cnId) {
                return $query->where('consignment_id', $cnId);
            })
            ->when($status, function ($query, $status) {
                return $query->where('status_id', $status);
            })
            ->when($merchantIds, function ($query) use ($merchantIds) {
                $query->whereIn('merchant_id', $merchantIds);
            })
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            })
            ->latest()
            ->paginate($perPage, ['*'], 'page', $page);
    }
}
