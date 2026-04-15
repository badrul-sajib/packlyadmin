<?php

namespace App\Http\Controllers\Admin\Shop;

use App\Caches\ShopSettingsCache;
use App\Http\Controllers\Controller;
use App\Models\Shop\ShopSetting;
use App\Services\Setting\ShopSettingService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class ShopSettingController extends Controller
{
    public function __construct(
        private readonly ShopSettingService $shopSettingService
    ) {
        $this->middleware('permission:website-setting')->only(['index', 'update']);
        $this->middleware('permission:system-configuration')->only(['systemSettings', 'update']);
        $this->middleware('permission:analytics-update')->only(['update']);
    }

    /**
     * @throws Throwable
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $settings = $this->shopSettingService->getShopSettingByGroupName($request->group);

            return view('components.setting.form', compact('settings'))->render();
        }

        $settingsGroups = $this->shopSettingService->getStatusBySettingsGroups(1);

        return view('Admin::setting.index', compact('settingsGroups'));

    }

    public function update(Request $request)
    {
        $data = $request->except('_token'); // Exclude the CSRF token

        foreach ($data as $key => $value) {
            $setting = ShopSetting::where('key', $key)->first();
            if ($setting) {
                if ($setting->type === 'file' && $request->hasFile($key)) {
                    // Handle file upload
                    // add validation for file type only jpeg,png
                    $request->validate([
                        $key => 'file|mimes:jpeg,png,jpg,gif,svg,avif,webp|max:2048',
                    ]);

                    $file = $request->file($key);
                    $extension = empty($file->getClientOriginalExtension()) ? $file->extension() : $file->getClientOriginalExtension();
                    $filePath = 'media/'.now()->format('Ymd_His').'_'.Str::uuid().'.'.$extension;
                    $value = Storage::disk(config('filesystems.default') ?? 'public')->put($filePath, file_get_contents($file), 'public');
                    if (! $value) {
                        throw new Exception('Media upload failed.');
                    }
                    $value = $filePath;

                }
                if ($setting->type === 'select' && $setting->options === 'custom_location') {
                    $value = implode(',', $value);
                }
                $setting->value = $value;
                $setting->save();
            }
        }

        ShopSettingsCache::invalidate();

        return response()->json(['message' => 'Settings updated successfully!']);
    }

    /**
     * @throws Throwable
     */
    public function systemSettings(Request $request)
    {
        if ($request->ajax()) {
            $settings = $this->shopSettingService->getShopSettingByGroupName($request->group);

            return view('components.setting.form', compact('settings'))->render();
        }

        $settingsGroups = $this->shopSettingService->getStatusBySettingsGroups(2);

        return view('Admin::setting.system', compact('settingsGroups'));
    }
}
