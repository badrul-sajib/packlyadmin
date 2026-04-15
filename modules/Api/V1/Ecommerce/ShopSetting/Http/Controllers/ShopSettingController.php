<?php

namespace Modules\Api\V1\Ecommerce\ShopSetting\Http\Controllers;

use App\Actions\FetchShopSetting;
use App\Http\Controllers\Controller;
use App\Models\Shop\ShopSetting;
use Illuminate\Http\Request;

class ShopSettingController extends Controller
{
    public function index(Request $request)
    {
        $data = (new FetchShopSetting)->execute();

        return success('Shop Setting fetched successfully', $data);
    }

    public function getBusinessManagerMaintenance(Request $request)
    {
        $settings = ShopSetting::where('group_name', 'Application Setting')
            ->whereIn('key', [
                'business_manager_ios_app_maintenance',
                'business_manager_android_app_maintenance',
            ])
            ->pluck('value', 'key');

        $platformFlags = [
            'android' => filter_var(
                $settings->get('business_manager_android_app_maintenance', '0'),
                FILTER_VALIDATE_BOOLEAN
            ),
            'ios' => filter_var(
                $settings->get('business_manager_ios_app_maintenance', '0'),
                FILTER_VALIDATE_BOOLEAN
            ),
        ];

        $platformHeader = strtolower(
            $request->header('platform') ??
                $request->header('x-platform') ??
                $request->header('x-client-platform') ??
                ''
        );

        if ($platformHeader && isset($platformFlags[$platformHeader])) {
            return success('Business manager maintenance flag fetched', [
                'platform' => $platformHeader,
                'maintenance' => $platformFlags[$platformHeader],
            ]);
        }

        return success('Business manager maintenance flags fetched', $platformFlags);
    }

    public function getAnalyticsTags()
    {
        $keys = [
            'gtm_ids',
            'ga_measurement_ids',
            'fb_pixel_ids',
            'tiktok_pixel_ids',
            'tawk_ids',
        ];

        $settings = ShopSetting::whereIn('key', $keys)->pluck('value', 'key');

        // Clean helper to explode and trim, and ignore empty values
        $explodeCSV = function ($string) {
            $items = collect(explode(',', $string))
                ->map(fn($id) => trim($id))
                ->filter()
                ->values();

            return $items->isEmpty() ? [] : $items->all();
        };

        return response()->json([
            'google_tag_manager_ids' => $explodeCSV($settings['gtm_ids'] ?? ''),
            'google_analytics_ids' => $explodeCSV($settings['ga_measurement_ids'] ?? ''),
            'facebook_pixel_ids' => $explodeCSV($settings['fb_pixel_ids'] ?? ''),
            'tiktok_pixel_ids' => $explodeCSV($settings['tiktok_pixel_ids'] ?? ''),
            'tawk_ids' => $explodeCSV($settings['tawk_ids'] ?? ''),
        ]);
    }
}
