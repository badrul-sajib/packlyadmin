<?php

namespace App\Actions;

use App\Enums\PopularShopStatus;
use App\Http\Resources\Ecommerce\PopularShopResource;
use App\Models\Shop\PopularShop;

class FetchPopularShops
{
    public function execute($request)
    {
        $validated = validator($request->only(['per_page', 'page', 'search']), [
            'per_page' => 'nullable|integer|min:1|max:100',
            'page'     => 'nullable|integer|min:1',
            'search'   => 'nullable|string|max:255',
        ])->validate();

        $perPage = $validated['per_page'] ?? 10;
        $page    = $validated['page']     ?? 1;
        $search  = $validated['search']   ?? null;

        $shops = PopularShop::query()
            ->where('is_active', PopularShopStatus::ACTIVE->value)
            // 🔹 Only include popular shops whose merchant is active and matches search
            ->whereHas('merchant', function ($query) use ($search) {
                $query->where('shop_status', 1)
                    ->when($search, function ($q) use ($search) {
                        $q->where(function ($sub) use ($search) {
                            $sub->where('shop_name', 'like', "%{$search}%")
                                ->orWhere('slug', 'like', "%{$search}%");
                        });
                    });
            })
            // 🔹 Eager load merchants + their settings
            ->with([
                'merchant.settings',
            ])
            ->orderBy('display_order')
            ->paginate($perPage, ['*'], 'page', $page);

        return formatPagination(
            'Shops fetched successfully',
            PopularShopResource::collection($shops)
        );
    }
}
