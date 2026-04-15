<?php

namespace Modules\Api\V1\Merchant\PickupAddress\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PickupAddressRequest extends FormRequest
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
            'police_station_name' => 'required|string|max:255',
            'police_station_id' => 'required|integer|min:1',
            'city_id' => 'required|integer|min:1',
            'city_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'contact_number' => [
                'required',
                'regex:/^01[3-9][0-9]{8}$/',
            ],
        ];
    }
}
