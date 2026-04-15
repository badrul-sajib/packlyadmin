<?php

namespace Modules\Api\V1\Ecommerce\Cart\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class CartStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items'                        => 'required|array|min:1',
            'items.*.product_id'           => ['required', 'exists:products,id'],
            'items.*.product_variation_id' => ['nullable', 'exists:product_variations,id'],
            'items.*.sku'                  => ['nullable', 'string', 'exists:product_variations,sku'],
            'items.*.action'               => ['required', 'in:increase,decrease,set,select'],
            'items.*.quantity'             => ['required_if:items.*.action,increase,decrease,set', 'integer', 'min:1'],
            'items.*.is_select'            => ['sometimes', 'boolean',  'required_if:items.*.action,select'],
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'The given data was invalid.',
            'errors'  => $validator->errors(),
        ], Response::HTTP_UNPROCESSABLE_ENTITY));
    }
}
