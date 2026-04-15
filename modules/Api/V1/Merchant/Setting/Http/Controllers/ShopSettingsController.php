<?php

namespace Modules\Api\V1\Merchant\Setting\Http\Controllers;

use App\Enums\MerchantStatus;
use App\Http\Controllers\Controller;
use App\Jobs\PushNotification;
use App\Models\Merchant\Merchant;
use App\Models\Merchant\MerchantSetting;
use App\Models\Review\Review;
use App\Models\Setting\ShopSetting;
use App\Models\Shop\ShopUpdateRequest;
use App\Services\ApiResponse;
use App\Services\ShopSettingService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Modules\Api\V1\Merchant\Setting\Http\Requests\ShopAddressRequest;
use Modules\Api\V1\Merchant\Setting\Http\Requests\ShopSettingsRequest;
use Modules\Api\V1\Merchant\Setting\Http\Resources\ShopProductResource;
use Symfony\Component\HttpFoundation\Response;

class ShopSettingsController extends Controller
{
    public function __construct(private readonly ShopSettingService $shopSettingService)
    {
        $this->middleware('shop.permission:show-shop-settings')->only('index', 'show');
        $this->middleware('shop.permission:update-shop-settings')->only('update', 'updateShopName', 'updateShopRequest');
    }

    public function index(Request $request): JsonResponse
    {
        $merchant = $request->user()->merchant;
        $settings = MerchantSetting::where('merchant_id', $merchant->id)->get();
        $formattedSettings = [];

        foreach ($settings as $setting) {
            $value = $setting->value;
            if ($this->isImageSetting($setting->key)) {
                $value = Storage::disk('public')->url($setting->value);
            }

            if ($setting->key === 'shop_settings') {
                $decodedValue = json_decode($value, true); // true for associative array
                info($decodedValue);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $value = $decodedValue;
                }
            }
            $formattedSettings[$setting->key] = $value;
        }

        return ApiResponse::success('Shop settings retrieved successfully', $formattedSettings, Response::HTTP_OK);
    }

    public function show(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (! $user->merchant) {
                return ApiResponse::error('Merchant not found for this user', Response::HTTP_NOT_FOUND);
            }

            $merchant = Merchant::withCount('followers')->findOrFail($user->merchant->id);

            // Get shop settings
            $settings = MerchantSetting::firstWhere([
                'merchant_id' => $merchant->id,
                'key' => 'shop_settings',
            ]);
            $shopSettingPhoneNo = ShopSetting::where([
                'key' => 'help_center_number_1',
                'group_name' => 'Help Center'
            ])->value('value');

            if (! $settings) {
                $settings = MerchantSetting::create([
                    'merchant_id' => $merchant->id,
                    'key' => 'shop_settings',
                    'value' => json_encode([]),
                ]);
            }

            $allSettings = json_decode($settings->value, true) ?? [];

            // Process product highlights
            $productHighlights = [];
            if (isset($allSettings['product_highlights']['products'])) {
                $productHighlights = ShopProductResource::collection(
                    $this->shopSettingService->getShopProductsByIds($allSettings['product_highlights']['products'])
                );
            }

            // Process promotional banner
            $promotionalBanner = $allSettings['promotional_banner'] ?? ['mobile' => [], 'desktop' => []];

            // Process shop logo and cover
            $shopLogoAndCover = $allSettings['shop_logo_and_cover'] ?? ['mobile_cover' => '', 'desktop_cover' => '', 'shop_logo' => ''];

            // Legacy format for backward compatibility
            $legacySettings = $allSettings['legacy'] ?? [];
            $trendingProducts = $this->processTrendingProducts($legacySettings);

            foreach (['mobile', 'desktop'] as $platform) {
                if (isset($legacySettings[$platform]['trending_products'])) {
                    unset($legacySettings[$platform]['trending_products']);
                }
            }

            $shop_status = $merchant->shop_status == MerchantStatus::Active ? 1 : 0;
            // app_e_commerce_url
            $shop_live_url = '';

            if ($shop_status) {
                $shop_live_url = ShopSetting::where('key', 'app_e_commerce_url')->first()->value ?? '';
                $shop_live_url = "{$shop_live_url}/shop/{$merchant->slug}";
            }

            $keyMap = [
                'delivery_charge' => 'ed_delivery_fee',
                'shipping_fee_isd' => 'id_delivery_fee',
                'shipping_fee_osd' => 'od_delivery_fee',
            ];

            $settings = ShopSetting::whereIn('key', array_keys($keyMap))->pluck('value', 'key');

            $shippingData = [];

            foreach ($keyMap as $dbKey => $label) {
                $shippingData[$label] = $settings[$dbKey] ?? 0;
            }

            $data = [
                'id' => $merchant->id,
                'name' => $merchant->shop_name,
                'address' => $merchant->shop_address,
                'map_address' => $merchant->map_address,
                'latitude' => $merchant->latitude,
                'longitude' => $merchant->longitude,
                'url' => $merchant->shop_url,
                'followers_count' => $merchant->followers_count,
                'ship_on_time' => 95,
                'chat_response_time' => 88,
                'shop_rating' => $this->shopRating($merchant),
                'joined_date' => optional($merchant->created_at)->diffForHumans(),
                'trusted_shop' => 100,
                'shop_settings' => $legacySettings,
                'product_highlights' => $productHighlights,
                'promotional_banner' => $promotionalBanner,
                'shop_logo_and_cover' => $shopLogoAndCover,
                'trending_products' => $trendingProducts,
                'shop_status' => $shop_status,
                'shop_status_seen' => $merchant->shop_status_seen,
                'shop_live_url' => $shop_live_url,
                'verification_info' => (object) [
                    'is_verified' => $merchant->is_verified->value ?? 1,
                    'is_verified_label' => $merchant->is_verified->label() ?? 1,
                    'nid_front_image' => $merchant->nid_front_image ?? '',
                    'nid_back_image' => $merchant->nid_back_image ?? '',
                    'trade_license_images' => $merchant->trade_license_images ?? '',
                    'bank_statement_images' => $merchant->bank_statement_images ?? '',
                ],
                'delivery_fees' => $shippingData,
                'shop_setting_phone_no' => $shopSettingPhoneNo ?? '',
            ];

            return ApiResponse::success('Shop settings retrieved successfully', $data, Response::HTTP_OK);
        } catch (ModelNotFoundException) {
            return ApiResponse::error('Merchant not found', Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            Log::error('Failed to retrieve shop settings: ' . $e->getMessage());

            return ApiResponse::error('Failed to retrieve shop settings', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    protected function processTrendingProducts(array $shopSettings): array
    {
        $trendingProducts = ['desktop' => [], 'mobile' => []];

        if (! empty($shopSettings['mobile']['trending_products'])) {
            $productIds = is_array($shopSettings['mobile']['trending_products'])
                ? $shopSettings['mobile']['trending_products']
                : json_decode($shopSettings['mobile']['trending_products'], true);

            if (json_last_error() === JSON_ERROR_NONE && ! empty($productIds)) {
                $trendingProducts['mobile'] = ShopProductResource::collection(
                    $this->shopSettingService->getShopProductsByIds($productIds)
                );
            }
        }

        if (! empty($shopSettings['desktop']['trending_products'])) {
            $productIds = is_array($shopSettings['desktop']['trending_products'])
                ? $shopSettings['desktop']['trending_products']
                : json_decode($shopSettings['desktop']['trending_products'], true);

            if (json_last_error() === JSON_ERROR_NONE && ! empty($productIds)) {
                $trendingProducts['desktop'] = ShopProductResource::collection(
                    $this->shopSettingService->getShopProductsByIds($productIds)
                );
            }
        }

        return $trendingProducts;
    }

    public function update(ShopSettingsRequest $request)
    {
        $merchant = auth()->user()->merchant;

        $rules = $request->validated();

        $messages = $request->messages();

        Validator::make($request->all(), $rules, $messages)
            ->after(function ($validator) use ($request, $merchant) {
                if ($request->setting_type === 'promotional_banner') {
                    // Get current settings to validate banner removals
                    $settings = MerchantSetting::firstWhere([
                        'merchant_id' => $merchant->id,
                        'key' => 'shop_settings',
                    ]);
                    $allSettings = $settings ? json_decode($settings->value, true) : [];
                    $currentBanners = $allSettings['promotional_banner'] ?? ['mobile' => [], 'desktop' => []];

                    // Validate mobile banner unique_ids exist
                    if ($request->has('remove_mobile_banners')) {
                        $removeIds = $request->input('remove_mobile_banners', []);
                        $existingIds = array_column($currentBanners['mobile'] ?? [], 'unique_id');
                        $invalidIds = array_diff($removeIds, $existingIds);
                        if (! empty($invalidIds)) {
                            $validator->errors()->add('remove_mobile_banners', 'Mobile banner unique IDs not found: ' . implode(', ', $invalidIds));
                        }
                    }

                    // Validate desktop banner unique_ids exist
                    if ($request->has('remove_desktop_banners')) {
                        $removeIds = $request->input('remove_desktop_banners', []);
                        $existingIds = array_column($currentBanners['desktop'] ?? [], 'unique_id');
                        $invalidIds = array_diff($removeIds, $existingIds);
                        if (! empty($invalidIds)) {
                            $validator->errors()->add('remove_desktop_banners', 'Desktop banner unique IDs not found: ' . implode(', ', $invalidIds));
                        }
                    }

                    // Calculate remaining banners after removal
                    $remainingMobile = $currentBanners['mobile'] ?? [];
                    $remainingDesktop = $currentBanners['desktop'] ?? [];

                    if ($request->has('remove_mobile_banners')) {
                        $removeIds = $request->input('remove_mobile_banners', []);
                        $remainingMobile = array_filter($remainingMobile, function ($banner) use ($removeIds) {
                            return ! in_array($banner['unique_id'] ?? '', $removeIds);
                        });
                    }

                    if ($request->has('remove_desktop_banners')) {
                        $removeIds = $request->input('remove_desktop_banners', []);
                        $remainingDesktop = array_filter($remainingDesktop, function ($banner) use ($removeIds) {
                            return ! in_array($banner['unique_id'] ?? '', $removeIds);
                        });
                    }

                    // Check if at least one banner will exist
                    $newMobileCount = count($request->file('mobile_banners', []));
                    $newDesktopCount = count($request->file('desktop_banners', []));

                    if (empty($remainingMobile) && empty($remainingDesktop) && $newMobileCount === 0 && $newDesktopCount === 0) {
                        $validator->errors()->add('mobile_banners', 'At least one banner (mobile or desktop) must be present.');
                    }
                }

                // Validate shop logo and cover unique_ids exist
                if ($request->setting_type === 'shop_logo_and_cover') {
                    $settings = MerchantSetting::firstWhere([
                        'merchant_id' => $merchant->id,
                        'key' => 'shop_settings',
                    ]);
                    $allSettings = $settings ? json_decode($settings->value, true) : [];
                    $currentShopData = $allSettings['shop_logo_and_cover'] ?? [];

                    // Validate mobile cover unique_id
                    if ($request->has('remove_mobile_cover')) {
                        $removeId = $request->input('remove_mobile_cover');
                        $currentId = $currentShopData['mobile_cover']['unique_id'] ?? '';
                        if ($removeId !== $currentId) {
                            $validator->errors()->add('remove_mobile_cover', 'Mobile cover unique ID not found.');
                        }
                    }

                    // Validate desktop cover unique_id
                    if ($request->has('remove_desktop_cover')) {
                        $removeId = $request->input('remove_desktop_cover');
                        $currentId = $currentShopData['desktop_cover']['unique_id'] ?? '';
                        if ($removeId !== $currentId) {
                            $validator->errors()->add('remove_desktop_cover', 'Desktop cover unique ID not found.');
                        }
                    }

                    // Validate shop logo unique_id
                    if ($request->has('remove_shop_logo')) {
                        $removeId = $request->input('remove_shop_logo');
                        $currentId = $currentShopData['shop_logo']['unique_id'] ?? '';
                        if ($removeId !== $currentId) {
                            $validator->errors()->add('remove_shop_logo', 'Shop logo unique ID not found.');
                        }
                    }
                }
            });

        $settingType = $request->input('setting_type');
        $settings = MerchantSetting::firstWhere([
            'merchant_id' => $merchant->id,
            'key' => 'shop_settings',
        ]);
        $allSettings = $settings ? json_decode($settings->value, true) : [];

        switch ($settingType) {
            case 'product_highlights':
                $allSettings['product_highlights'] = [
                    'products' => $request->input('products', []),
                ];

                break;

            case 'promotional_banner':
                $currentBanners = $allSettings['promotional_banner'] ?? ['mobile' => [], 'desktop' => []];

                // Handle mobile banners
                $mobileBanners = $currentBanners['mobile'] ?? [];

                // Remove specified mobile banners
                if ($request->has('remove_mobile_banners')) {
                    $removeIds = $request->input('remove_mobile_banners', []);
                    $mobileBanners = array_filter($mobileBanners, function ($banner) use ($removeIds) {
                        $shouldRemove = in_array($banner['unique_id'] ?? '', $removeIds);
                        if ($shouldRemove) {
                            $this->deleteImage($banner['image'] ?? '');
                        }

                        return ! $shouldRemove;
                    });
                    $mobileBanners = array_values($mobileBanners);
                }

                // Add new mobile banners
                if ($request->hasFile('mobile_banners')) {
                    $newMobileBanners = $this->processBannerFiles($request->file('mobile_banners'));
                    $mobileBanners = array_merge($mobileBanners, $newMobileBanners);
                }

                // Handle desktop banners
                $desktopBanners = $currentBanners['desktop'] ?? [];

                // Remove specified desktop banners
                if ($request->has('remove_desktop_banners')) {
                    $removeIds = $request->input('remove_desktop_banners', []);
                    $desktopBanners = array_filter($desktopBanners, function ($banner) use ($removeIds) {
                        $shouldRemove = in_array($banner['unique_id'] ?? '', $removeIds);
                        if ($shouldRemove) {
                            $this->deleteImage($banner['image'] ?? '');
                        }

                        return ! $shouldRemove;
                    });
                    $desktopBanners = array_values($desktopBanners);
                }

                // Add new desktop banners
                if ($request->hasFile('desktop_banners')) {
                    $newDesktopBanners = $this->processBannerFiles($request->file('desktop_banners'));
                    $desktopBanners = array_merge($desktopBanners, $newDesktopBanners);
                }

                $allSettings['promotional_banner'] = [
                    'mobile' => $mobileBanners,
                    'desktop' => $desktopBanners,
                ];

                break;

            case 'shop_logo_and_cover':
                $currentShopData = $allSettings['shop_logo_and_cover'] ?? [
                    'mobile_cover' => ['image' => '', 'unique_id' => ''],
                    'desktop_cover' => ['image' => '', 'unique_id' => ''],
                    'shop_logo' => ['image' => '', 'unique_id' => ''],
                ];

                // Handle mobile cover
                $mobileCover = $currentShopData['mobile_cover'];
                if ($request->has('remove_mobile_cover')) {
                    $this->deleteImage($mobileCover['image'] ?? '');
                    $mobileCover = ['image' => '', 'unique_id' => ''];
                }
                if ($request->hasFile('mobile_cover')) {
                    if (! empty($mobileCover['image'])) {
                        $this->deleteImage($mobileCover['image']);
                    }
                    $mobileCover = $this->uploadImageWithId($request->file('mobile_cover'));
                }

                // Handle desktop cover
                $desktopCover = $currentShopData['desktop_cover'];
                if ($request->has('remove_desktop_cover')) {
                    $this->deleteImage($desktopCover['image'] ?? '');
                    $desktopCover = ['image' => '', 'unique_id' => ''];
                }
                if ($request->hasFile('desktop_cover')) {
                    if (! empty($desktopCover['image'])) {
                        $this->deleteImage($desktopCover['image']);
                    }
                    $desktopCover = $this->uploadImageWithId($request->file('desktop_cover'));
                }

                // Handle shop logo
                $shopLogo = $currentShopData['shop_logo'];
                if ($request->has('remove_shop_logo')) {
                    $this->deleteImage($shopLogo['image'] ?? '');
                    $shopLogo = ['image' => '', 'unique_id' => ''];
                }
                if ($request->hasFile('shop_logo')) {
                    if (! empty($shopLogo['image'])) {
                        $this->deleteImage($shopLogo['image']);
                    }
                    $shopLogo = $this->uploadImageWithId($request->file('shop_logo'));
                }

                $allSettings['shop_logo_and_cover'] = [
                    'mobile_cover' => $mobileCover,
                    'desktop_cover' => $desktopCover,
                    'shop_logo' => $shopLogo,
                ];

                break;
        }

        MerchantSetting::updateOrCreate(
            ['merchant_id' => $merchant->id, 'key' => 'shop_settings'],
            ['value' => json_encode($allSettings)]
        );

        return ApiResponse::success('Shop settings updated successfully', [], Response::HTTP_OK);
    }

    // Helper method to delete image files
    private function deleteImage($imageUrl)
    {
        if (empty($imageUrl)) {
            return;
        }
        $diskName = config('filesystems.default', 'public');

        if ($diskName === 's3') {
            $path = parse_url($imageUrl, PHP_URL_PATH);
        } else {
            $path = str_replace(url('/storage/'), '', $imageUrl);
        }

        Storage::disk($diskName)->delete($path);
    }

    // Helper method to generate 4-digit unique ID
    private function generateUniqueId()
    {
        return str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
    }

    private function processBannerFiles($files)
    {
        $banners = [];
        foreach ($files as $file) {
            $imageUrl = $this->uploadImage($file);
            if ($imageUrl) {
                $banners[] = [
                    'image' => $imageUrl,
                    'unique_id' => $this->generateUniqueId(),
                ];
            }
        }

        return $banners;
    }

    private function uploadImageWithId($file)
    {
        $imageUrl = $this->uploadImage($file);

        return [
            'image' => $imageUrl ?? '',
            'unique_id' => $imageUrl ? $this->generateUniqueId() : '',
        ];
    }

    private function uploadImage($file, $path = 'shop-settings'): ?string
    {
        if (! $file || ! $file->isValid()) {
            return null;
        }

        $diskName = config('filesystems.default', 'public');
        $extension = empty($file->getClientOriginalExtension()) ? $file->extension() : $file->getClientOriginalExtension();
        $filePath = 'media/' . now()->format('Ymd_His') . '_' . Str::uuid() . '.' . $extension;

        $result = Storage::disk($diskName)->put($filePath, file_get_contents($file), 'public');

        return $result ? Storage::disk($diskName)->url($filePath) : null;
    }

    public function updateShopName(Request $request): JsonResponse
    {
        $merchant = $request->user()->merchant;

        if (! empty($merchant->shop_name)) {
            return ApiResponse::error('You already have a shop name', Response::HTTP_CONFLICT);
        }

        $slug = Str::slug($request->name);

        if (Merchant::where('slug', $slug)->exists()) {
            return ApiResponse::error('Shop name already exists', Response::HTTP_CONFLICT);
        }

        $merchant->update(['shop_name' => $request->name, 'slug' => $slug]);

        return ApiResponse::success('Shop name updated successfully', [], Response::HTTP_OK);
    }

    public function updateShopRequest(Request $request): JsonResponse
    {
        $merchant = $request->user()->merchant;

        if (empty($request->shop_name) && empty($request->shop_url) && empty($request->shop_address)) {
            return ApiResponse::error('No information provided', Response::HTTP_NOT_FOUND);
        } elseif (! empty($request->shop_name) && $request->shop_name != $merchant->shop_name) {
            $shop = Merchant::where('shop_name', $request->shop_name)->first();
            if ($shop) {
                return ApiResponse::error('Shop name already exists', Response::HTTP_CONFLICT);
            }
        } elseif (! empty($request->shop_url) && $request->shop_url != $merchant->shop_url) {
            $shop = Merchant::where('shop_url', $request->shop_url)->first();
            if ($shop) {
                return ApiResponse::error('Shop url already exists', Response::HTTP_CONFLICT);
            }
        }

        if (Merchant::where('id', $merchant->id)
            ->where('shop_name', $request->shop_name)
            ->where('shop_url', $request->shop_url)
            ->where('shop_address', $request->shop_address)
            ->exists()
        ) {
            return ApiResponse::error('No changes detected', Response::HTTP_CONFLICT);
        }


        if (ShopUpdateRequest::where('merchant_id', $merchant->id)
            ->where('status', 'pending')
            ->exists()
        ) {
            return ApiResponse::error('You already have a pending shop update request', Response::HTTP_CONFLICT);
        }

        ShopUpdateRequest::create([
            'merchant_id' => $merchant->id,
            'old_name' => $merchant->shop_name,
            'old_address' => $merchant->shop_address,
            'old_link' => $merchant->shop_url,
            'name' => $request->shop_name,
            'link' => $request->shop_url,
            'address' => $request->shop_address,
            'status' => 'pending',
        ]);

        $notificationMessage = 'New Shop update request from ' . auth()->user()->name . '.';

        try {
            PushNotification::dispatch([
                'title' => 'New Shop Update Request',
                'message' => $notificationMessage,
                'type' => 'info',
                'action_url' => '/shop-update-requests',
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }

        return ApiResponse::success('Shop update requested, please wait for approval', Response::HTTP_OK);
    }

    public function shopProducts(): JsonResponse
    {
        try {
            $products = $this->shopSettingService->getShopProducts();

            return ApiResponse::success('Shop products retrieved successfully', [
                'products' => ShopProductResource::collection($products->items()),
                'pagination' => [
                    'total' => $products->total(),
                    'per_page' => $products->perPage(),
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                ],
            ]);
        } catch (Exception $e) {
            return ApiResponse::error('Failed to retrieve shop products', Response::HTTP_INTERNAL_SERVER_ERROR, ['error' => $e->getMessage()]);
        }
    }

    public function shopRating($merchant): string
    {
        $reviews = Review::whereIn('product_id', $merchant->products()->pluck('id'))->get();

        if ($reviews->isEmpty()) {
            return '0.00';
        }

        $averageSellerRating = $reviews->avg('seller_rating');
        $averageShippingRating = $reviews->avg('shipping_rating');
        $averageGeneralRating = $reviews->avg('rating');

        $combinedAverage = ($averageSellerRating + $averageShippingRating + $averageGeneralRating) / 3;
        $percentage = ($combinedAverage / 5) * 100;

        return number_format($percentage, 2);
    }

    private function isImageSetting(string $key): bool
    {
        $imageSettings = ['profile', 'logo', 'mobile_banner', 'web_banner'];

        return in_array($key, $imageSettings);
    }

    public function updateShopStatusSeen(): JsonResponse
    {
        try {
            $this->shopSettingService->updateShopStatusSeen();

            return ApiResponse::success('Shop status updated to seen', [], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Failed to update shop status seen: ' . $e->getMessage());

            return ApiResponse::error('Failed to update shop status seen', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateShopAddress(ShopAddressRequest $request): JsonResponse
    {
        try {
            $this->shopSettingService->updateShopAddress($request->validated());

            return ApiResponse::success('Shop address updated successfully', [], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Failed to update shop address: ' . $e->getMessage());

            return ApiResponse::error('Failed to update shop address', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
