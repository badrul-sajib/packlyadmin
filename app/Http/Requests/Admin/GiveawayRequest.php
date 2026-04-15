<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class GiveawayRequest extends FormRequest
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

        $isEdit = $this->route('giveaway')?->id !== null;

        return [
            'name' => 'required|string|max:100',
            'start_at' => $isEdit ? 'required|date' : 'required|date|after_or_equal:today',
            'end_at' => 'required|date|after:start_at',
            'gifts' => 'required|array|min:1|max:500',
            'gifts.*.name' => 'required|string|max:100',
            'gifts.*.quantity' => 'required|integer|min:1|max:1000000',
            'gifts.*.rank' => 'required|integer|min:1|max:1000',
        ];
    }
}
