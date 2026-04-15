<?php

namespace App\Actions;

use App\Models\Shop\ShopUpdateRequest;

class FetchShopUpdateRequest
{
    public function execute($request)
    {
        $perPage     = $request->perPage     ?? 10;
        $status      = $request->status      ?? null;
        $page        = $request->page        ?? 1;
        $search      = $request->search      ?? null;
        $search_type = $request->search_type ?? 'id';

        return ShopUpdateRequest::with('merchant', 'merchant.userRelation')
            ->when($search, function ($query) use ($search, $search_type) {
                if ($search_type === 'id') {
                    $query->whereHas('merchant', function ($q) use ($search) {
                        $q->where('id', $search);
                    });
                } else {
                    $query->whereHas('merchant', function ($q) use ($search) {
                        $q->where('shop_name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
                }
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->latest()
            ->select('id', 'merchant_id', 'old_name', 'old_address', 'old_link', 'name', 'address', 'link', 'status', 'created_at')
            ->paginate($perPage, ['*'], 'page', $page)
            ->withQueryString();
    }
}
