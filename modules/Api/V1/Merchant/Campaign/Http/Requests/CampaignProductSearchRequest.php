<?php

namespace Modules\Api\V1\Merchant\Campaign\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class CampaignProductSearchRequest extends FormRequest
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
            'prime_view_id' => 'required|exists:prime_views,id',
            'campaign_id' => 'required|exists:campaigns,id'
        ];
    }
}
