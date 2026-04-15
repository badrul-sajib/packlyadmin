<?php

namespace App\Http\Controllers\Admin\Merchant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Merchant\MerchantConfigurationRequest;
use App\Http\Requests\Admin\Merchant\MerchantVerificationRequest;
use App\Models\Merchant\Merchant;
use App\Services\Merchant\MerchantConfigurationService;
use Illuminate\Support\Facades\Log;

class MerchantConfigurationController extends Controller
{
    public function __construct(
        private MerchantConfigurationService $merchantConfigurationService
    ) {}

    public function update(MerchantConfigurationRequest $request, Merchant $merchant)
    {
        try {
            $this->merchantConfigurationService->merchantConfiguration($request->validated(), $merchant);

            return response()->json(['message' => 'Merchant configuration updated successfully.']);
        } catch (\Throwable $th) {
            Log::error('Error updating merchant configuration: '.$th->getMessage());

            return failure('Failed to update merchant configuration.', 500);
        }
    }

    public function verification(MerchantVerificationRequest $request, Merchant $merchant)
    {
        try {
            $this->merchantConfigurationService->verification($request->validated(), $merchant);

            return response()->json(['message' => 'Merchant configuration updated successfully.']);
        } catch (\Throwable $th) {
            Log::error('Error updating merchant configuration: '.$th->getMessage());

            return failure('Failed to update merchant configuration.', 500);
        }
    }
}
