<?php

namespace App\Http\Controllers\Admin\Shop;

use App\Caches\ShopSettingsCache;
use App\Http\Controllers\Controller;
use App\Models\Shop\ShopSetting;
use App\Services\Setting\ShopSettingService;
use Illuminate\Http\Request;

class ApplicationSettingController extends Controller
{
    public function __construct(
        private readonly ShopSettingService $shopSettingService
    ) {
        $this->middleware('permission:website-setting')->only(['index', 'update']);
    }

    public function index()
    {
        $settings = $this->shopSettingService->getShopSettingByGroupName('Application Setting');
        return view('backend.pages.application-settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->except('_token');

        \Illuminate\Support\Facades\Log::info('Updating Application Settings', ['data' => $data]);

        foreach ($data as $key => $value) {
            ShopSetting::where('key', $key)->update(['value' => $value]);
        }

        \Illuminate\Support\Facades\Cache::forget(ShopSettingsCache::CACHE_KEY);

        return redirect()->back()->with('success', 'Settings updated successfully!');
    }
}
