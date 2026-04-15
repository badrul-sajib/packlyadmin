<?php

namespace Modules\Api\V1\Ecommerce\Attribute\Http\Controllers;

use App\Services\ApiResponse;
use Modules\Api\V1\Ecommerce\Attribute\Services\AttributeService;
use Modules\Api\V1\Ecommerce\Attribute\Http\Requests\StoreAttributeRequest;
use Modules\Api\V1\Ecommerce\Attribute\Http\Requests\UpdateAttributeRequest;
use Modules\Api\V1\Ecommerce\Attribute\Http\Resources\AttributeResource;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class AttributeController extends Controller
{
    public function __construct(
        protected AttributeService $service
    ) {}

    public function index(): JsonResponse
    {
        try {
            $data = $this->service->paginate(15);
            return ApiResponse::success(
                'Attributes retrieved successfully',
                AttributeResource::collection($data)
            );
        } catch (\Throwable $e) {
            \Log::error($e->getMessage());
            return ApiResponse::error('Something went wrong');
        }
    }
}
