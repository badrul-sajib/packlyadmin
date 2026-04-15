<?php

namespace Modules\Api\V1\Ecommerce\Campaign\Http\Controllers;

use App\Models\PrimeView\PrimeView;
use App\Services\ApiResponse;
use Modules\Api\V1\Ecommerce\Campaign\Http\Resources\PrimeViewResource;
use Modules\Api\V1\Ecommerce\Campaign\Services\CampaignService;
use Modules\Api\V1\Ecommerce\Campaign\Http\Resources\CampaignResource;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class CampaignController extends Controller
{
    public function __construct(
        protected CampaignService $service
    ) {}

    public function show(string $slug): JsonResponse
    {
        try {
            $item = $this->service->findBySlug($slug);
            return ApiResponse::success(
                'Campaign retrieved',
                new CampaignResource($item)
            );
        } catch (\Throwable $e) {
            return ApiResponse::error('Campaign not found', 404);
        }
    }

    public function primeView(string $slug): JsonResponse
    {
        try {
            $item = PrimeView::where('slug', $slug)->first();

            return ApiResponse::success(
                'Prime view retrieved',
                new PrimeViewResource($item)
            );
        } catch (\Throwable $e) {
            return ApiResponse::error('Prime view not found', 404);
        }
    }
}
