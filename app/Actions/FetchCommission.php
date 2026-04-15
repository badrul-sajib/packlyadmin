<?php

namespace App\Actions;

use App\Models\Merchant\Commission;

class FetchCommission
{
    public function execute($request)
    {
        $status      = $request->status ?? '';
        $search      = $request->search ?? '';

        return Commission::with('category', 'product', 'merchant')
            ->when($search, function ($query) use ($search) {
                $query->whereHas('product', function ($query) use ($search) {
                    $query->whereAny(['name', 'sku'], 'like', "%{$search}%");
                })->orWhereHas('category', function ($query) use ($search) {
                    $query->whereAny(['name'], 'like', "%{$search}%");
                })->orWhereHas('merchant', function ($query) use ($search) {
                    $query->whereAny(['name', 'shop_name', 'phone'], 'like', "%{$search}%");
                });
            })
            ->when($status, function ($query) use ($status) {
                return $query->where('status', $status);
            })->get();
    }
}
