<?php

namespace Modules\Api\V1\Merchant\Campaign\Http\Requests;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class CampaignProductRequest extends FormRequest
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
            'campaign_id' => 'required|exists:campaigns,id',
            'prime_view_id' => 'required|exists:prime_views,id',
            'products' => 'required|array'
        ];
    }
}
