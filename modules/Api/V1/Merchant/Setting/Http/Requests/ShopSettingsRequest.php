<?php

namespace Modules\Api\V1\Merchant\Setting\Http\Requests;

use App\Enums\ShopProductStatus;
use App\Services\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class ShopSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $merchant = auth()->user()->merchant;

        return [
            'setting_type' => 'required|in:product_highlights,promotional_banner,shop_logo_and_cover',
            'products'     => [
                'nullable',
                'array',
                function ($attribute, $value, $fail) use ($merchant) {
                    if (empty($value)) {
                        return;
                    }

                    $productIds = array_map('intval', $value);
                    if (empty($productIds)) {
                        return;
                    }

                    $validCount = $merchant->shop_products()
                        ->where('status', ShopProductStatus::APPROVED->value)
                        ->where('active_status', 1)
                        ->whereIn('product_id', $productIds)
                        ->count();

                    if ($validCount !== count($productIds)) {
                        $fail('One or more product IDs do not belong to the merchant or are not active.');
                    }
                },
            ],
            'mobile_banners'    => 'nullable|array|max:4',
            'mobile_banners.*'  => 'nullable|file|image',
            'desktop_banners'   => 'nullable|array|max:4',
            'desktop_banners.*' => 'nullable|file|image',
            'mobile_cover'      => 'nullable|file|image',
            'desktop_cover'     => 'nullable|file|image',
            'shop_logo'         => 'nullable|file|image',

            'remove_mobile_banners'   => 'nullable|array|max:4',
            'remove_mobile_banners.*' => 'nullable|string|size:4',

            'remove_desktop_banners'   => 'nullable|array|max:4',
            'remove_desktop_banners.*' => 'nullable|string|size:4',

            'remove_mobile_cover'  => 'nullable|string|size:4',
            'remove_desktop_cover' => 'nullable|string|size:4',
            'remove_shop_logo'     => 'nullable|string|size:4',
        ];
    }

    public function messages(): array
    {
        return [
            'remove_mobile_banners.*.size'  => 'Mobile banner unique ID must be exactly 4 characters.',
            'remove_desktop_banners.*.size' => 'Desktop banner unique ID must be exactly 4 characters.',
            'remove_mobile_cover.size'      => 'Mobile cover unique ID must be exactly 4 characters.',
            'remove_desktop_cover.size'     => 'Desktop cover unique ID must be exactly 4 characters.',
            'remove_shop_logo.size'         => 'Shop logo unique ID must be exactly 4 characters.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            ApiResponse::validationError('Validation Failed', $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
