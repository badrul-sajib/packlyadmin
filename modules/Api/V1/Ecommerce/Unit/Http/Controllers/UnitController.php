<?php

namespace Modules\Api\V1\Ecommerce\Unit\Http\Controllers;

use App\Services\ApiResponse;
use Modules\Api\V1\Ecommerce\Unit\Services\UnitService;
use Modules\Api\V1\Ecommerce\Unit\Http\Requests\StoreUnitRequest;
use Modules\Api\V1\Ecommerce\Unit\Http\Requests\UpdateUnitRequest;
use Modules\Api\V1\Ecommerce\Unit\Http\Resources\UnitResource;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class UnitController extends Controller
{
    public function __construct(
        protected UnitService $service
    ) {}

    public function index(): JsonResponse
    {
        try {
            return UnitService::getUnits();
        } catch (\Throwable $e) {
            //   \Log::error($e->getMessage());
            return ApiResponse::error('Something went wrong');
        }
    }
}
