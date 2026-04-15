<?php

namespace Modules\Api\V1\Ecommerce\Auth\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Carbon;

class UpdateUserRequest extends FormRequest
{
    public function rules(): array
    {
        $user = auth()->user();

        return [
            'name'          => 'sometimes|required|string|max:255',
            'email'         => 'nullable|email|unique:mysql_external.users,email,'.$user->id,
            'date_of_birth' => 'nullable|date|before_or_equal:'.Carbon::now()->subYears(13)->startOfDay()->format('Y-m-d'),
            'avatar'        => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'gender'        => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'date_of_birth.before_or_equal' => 'You need to be at least 13 years old.',
            'date_of_birth.date'            => 'Please enter a valid date for your date of birth.',
            'email.unique'                  => 'This email is already in use.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            validationError('Validation Error', $validator->errors())
        );
    }

    public function authorize(): bool
    {
        return true;
    }
}
