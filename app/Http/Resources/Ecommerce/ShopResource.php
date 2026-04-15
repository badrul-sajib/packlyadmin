<?php

namespace App\Http\Resources\Ecommerce;

use App\Models\Merchant\MerchantSetting;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Number;

class ShopResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $shopData = $this->settings->firstWhere('key', 'shop_settings');

        if (! $shopData) {
            $shopData = MerchantSetting::create([
                'merchant_id' => $this->id,
                'key'         => 'shop_settings',
                'value'       => json_encode([]),
            ]);
        }

        $settings = json_decode($shopData->value, true) ?? [];

        $images = [
            'mobile' => [
                'logo'  => $settings['shop_logo_and_cover']['shop_logo']['image']    ?? '',
                'cover' => $settings['shop_logo_and_cover']['mobile_cover']['image'] ?? '',
            ],
            'desktop' => [
                'logo'  => $settings['shop_logo_and_cover']['shop_logo']['image']     ?? '',
                'cover' => $settings['shop_logo_and_cover']['desktop_cover']['image'] ?? '',
            ],
        ];

        return [
            'shop_id'            => $this->id,
            'shop_name'          => $this->shop_name,
            'shop_slug'          => $this->slug,
            'shop_url'           => $this->shop_url,
            'followers'          => Number::abbreviate($this->followers_count ?? 0, $this->followers_count < 1000 ? 0 : 1),
            'shop_rating'        => $this->shop_rating_star,
            'shop_rating_scale'  => '5.0',
            'shop_image'         => $images,
        ];
    }
}
