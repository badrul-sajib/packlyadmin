<?php

namespace App\Services\Setting;

use App\Models\Shop\ShopSetting;
use Illuminate\Database\Eloquent\Collection;

class ShopSettingService
{
    public function getStatusBySettingsGroups(int $status = 1, array $withOutGroup = ['Analytics', 'Application Setting']): Collection
    {
        return ShopSetting::query()
            ->when($withOutGroup, function ($query) use ($withOutGroup) {
                return $query->whereNotIn('group_name', $withOutGroup);
            })
            ->where('status', $status)
            ->orderBy('order')
            ->get()->groupBy('group_name');
    }

    public function getShopSettingByGroupName(string $groupName): Collection
    {
        return ShopSetting::where('group_name', $groupName)->get();
    }
}
