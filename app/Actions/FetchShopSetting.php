<?php

namespace App\Actions;

use App\Models\Shop\ShopSetting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FetchShopSetting
{
    public function execute(): Collection
    {
        $settings = ShopSetting::get()->map(function ($setting) {
            $value = $setting->value;

            if (is_string($value) && ! str_starts_with($value, 'http') && str_starts_with($value, 'uploads/')) {
                $value = Storage::url($value);
            }

            return [
                'group_name' => $setting->group_name,
                'key' => $setting->key,
                'value' => $value,
            ];
        });

        $prices = $this->minAndMaxProductPrice();

        foreach ($prices as $key => $value) {
            $settings->push(['group_name' => 'price', 'key' => $key, 'value' => $value]);
        }

        return $settings;
    }

    public function minAndMaxProductPrice(): array
    {
        $getPrices = fn ($table) => DB::table($table)
            ->selectRaw('
            MIN(CASE WHEN e_discount_price IS NULL OR e_discount_price = 0 THEN e_price ELSE e_discount_price END) as min_price,
            MAX(CASE WHEN e_discount_price IS NULL OR e_discount_price = 0 THEN e_price ELSE e_discount_price END) as max_price
        ')->first();

        $p1 = $getPrices('shop_products');
        $p2 = $getPrices('shop_product_variations');

        return [
            'min_price' => min($p1->min_price ?? 0, $p2->min_price ?? 0),
            'max_price' => max($p1->max_price ?? 0, $p2->max_price ?? 0),
        ];
    }
}
