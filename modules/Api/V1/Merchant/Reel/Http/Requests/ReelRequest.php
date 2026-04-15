<?php

namespace Modules\Api\V1\Merchant\Reel\Http\Requests;

use App\Enums\MerchantStatus;
use App\Models\Product\Product;
use App\Services\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class ReelRequest extends FormRequest
{
    public function rules(): array
    {
        if ($this->isMethod('post')) {
            return [
                'title'                 => 'nullable|string|max:255',
                'link'                  => 'nullable|string',
                'description'           => 'nullable|string',
                'image'                 => 'nullable|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
                'video'                 => 'nullable|mimetypes:video/quicktime,video/mp4,video/x-m4v,video/*|max:10000',
                'enable_buy_now_button' => 'sometimes|boolean',
                'buy_now_type'          => 'nullable|in:store,product|required_if:enable_buy_now_button,true',
                'product_id'            => 'nullable|exists:products,id|required_if:buy_now_type,product',
                'thumbnail_image'       => 'nullable|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            ];
        }

        if (in_array($this->method(), ['PUT', 'PATCH'])) {
            return [
                'title'                 => 'required|string|max:255',
                'link'                  => 'nullable|url',
                'description'           => 'nullable|string',
                'image'                 => 'nullable|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
                'enable_buy_now_button' => 'sometimes|boolean',
                'buy_now_type'          => 'nullable|in:store,product|required_if:enable_buy_now_button,true',
                'product_id'            => 'nullable|exists:products,id|required_if:buy_now_type,product',
                'thumbnail_image'       => 'nullable|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            ];
        }

        return [];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $merchant  = auth()->user()?->merchant;
            $productId = $this->input('product_id');

            // Shop active check
            if ($merchant?->shop_status != MerchantStatus::Active) {
                $validator->errors()->add('shop_status', 'Shop inactive. Ask admin to activate before creating reels.');
            }

            if ($productId && $merchant?->id) {
                // 1. Check product ownership
                $belongsToMerchant = Product::where('id', $productId)
                    ->where('merchant_id', $merchant->id)
                    ->exists();

                if (! $belongsToMerchant) {
                    $validator->errors()->add('product_id', 'The selected product does not belong to your store.');
                }

                // 2. Check product availability in shop
                $existInShopProduct = $merchant->shop_products()
                    ->where('product_id', $productId)
                    ->where('active_status', '1')
                    ->where('status', '2')
                    ->exists();

                if (! $existInShopProduct) {
                    $validator->errors()->add('product_id', 'The selected product is not available in shop.');
                }
            }
        });
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            ApiResponse::validationError('Validation Failed', $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }

    public function authorize(): bool
    {
        return true;
    }
}
