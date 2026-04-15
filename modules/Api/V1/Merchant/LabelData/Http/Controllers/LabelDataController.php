<?php

namespace Modules\Api\V1\Merchant\LabelData\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Shop\ShopSetting;
use App\Services\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class LabelDataController extends Controller
{

    public function getLabel(): JsonResponse
    {
        $merchant = Auth::user()?->merchant;

        $shop_settings = ShopSetting::whereIn('key', [
            'site_logo',
            'help_center_number_1',
            'help_center_number_2',
            'help_center_address',
        ])
            ->pluck('value', 'key')
            ->toArray();

        $data = [
            'shop_name'   => $merchant->shop_name,
            'location'    => $merchant->shop_address,
            'hotline'     => $merchant->phone,
            'cashier'     => null,
            'terminal'    => null,
            'outlet'      => null,
            'packly_info' => [
                'logo'    => $shop_settings['site_logo'] ? url('storage/'.$shop_settings['site_logo']) : null,
                'website' => config('app.e_commerce_url'),
                'phone_1' => $shop_settings['help_center_number_1'] ?? null,
                'phone_2' => $shop_settings['help_center_number_2'] ?? null,
                'address' => $shop_settings['help_center_address']  ?? null,
            ],
        ];

        return ApiResponse::success('Label data fetched successfully.', $data);
    }
}
