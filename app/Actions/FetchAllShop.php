<?php

namespace App\Actions;

use App\Enums\MerchantStatus;
use App\Models\Merchant\Merchant;

class FetchAllShop
{
    public function execute($request)
    {
        $validated = validator($request->only(['per_page', 'page', 'search']), [
            'per_page' => 'nullable|integer|min:1|max:100',
            'page'     => 'nullable|integer|min:1',
            'search'   => 'nullable|string',
        ])->validate();

        $perPage = $validated['per_page']    ?? 10;
        $page    = $validated['page']        ?? 1;
        $search  = $validated['search']      ?? null;

        $shops = Merchant::where('shop_status', MerchantStatus::Active->value)
            ->when($search, function ($query, $search) {
                $query->where('shop_name', 'like', '%'.$search.'%')
                    ->orWhere('slug', 'like', '%'.$search.'%');
            })
            ->withCount('followers')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        return $shops;
    }
}
