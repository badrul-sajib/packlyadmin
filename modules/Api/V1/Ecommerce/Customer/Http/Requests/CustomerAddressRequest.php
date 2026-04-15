<?php

namespace Modules\Api\V1\Ecommerce\Customer\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class CustomerAddressRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'location_id' => ['nullable', Rule::exists('locations', 'id')->where(function ($query) {
                $query->where('type', 'city');
            }),],
            'name' => [
                'nullable',
                'min:3',
                'max:25',
                'regex:/^[\p{Bengali}A-Za-z0-9\s\.,\-\'"]+$/u'
            ],
            'landmark'        => 'nullable|max:100',
            'address' => [
                'required',
                'max:255',
                'regex:/^[\p{Bengali}A-Za-z0-9\s\.,\-\/#()\'"]+$/u'
            ],
            'address_type'    => 'nullable|in:home,office,business,education,others',
            'contact_number'  => ['required', 'max:15', 'regex:/^01[3-9]\d{8}$/'],
            'is_default_bill' => 'nullable|in:0,1',
            'is_default_ship' => 'nullable|in:0,1',
        ];
    }

    public function failedValidation(Validator $validator): JsonResponse
    {
        $errors = $validator->errors();

        throw new HttpResponseException(validationError('Validation Error', errors: $errors));
    }

    public function messages(): array
    {
        return [
            'location_id.exists' => 'Please select a valid city',
        ];
    }
}
