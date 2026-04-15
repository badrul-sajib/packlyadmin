<?php

namespace App\Http\Resources\Ecommerce;

use App\Models\Merchant\MerchantSetting;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class PopularShopResource extends JsonResource
{
    public function toArray($request)
    {
        $merchant = $this->merchant;

        if (! $merchant) {
            return null;
        }

        // Fetch or initialize shop settings
        $shopData = $merchant->settings->firstWhere('key', 'shop_settings');

        if (! $shopData) {
            $shopData = MerchantSetting::create([
                'merchant_id' => $merchant->id,
                'key'         => 'shop_settings',
                'value'       => json_encode([]),
            ]);
        }

        $settings = json_decode($shopData->value, true) ?? [];

        $images = [
            'mobile' => [
                'logo'  => Arr::get($settings, 'shop_logo_and_cover.shop_logo.image', ''),
                'cover' => Arr::get($settings, 'shop_logo_and_cover.mobile_cover.image', ''),
            ],
            'desktop' => [
                'logo'  => Arr::get($settings, 'shop_logo_and_cover.shop_logo.image', ''),
                'cover' => Arr::get($settings, 'shop_logo_and_cover.desktop_cover.image', ''),
            ],
        ];

        $ratings = $this->ratings ?? [];

        return [
            'id'           => $merchant->id,
            'shop_name'    => $merchant->shop_name,
            'shop_slug'    => $merchant->slug,
            'shop_rating'  => $merchant->shop_rating_star ?? '0',
            'rating_count' => $merchant->rating_count     ?? '0',
            'followers'    => $merchant->followers_count  ?? 0,
            'shop_image'   => $images,
        ];
    }
}
