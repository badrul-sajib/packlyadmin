<?php

namespace Modules\Api\V1\Merchant\Issue\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Api\V1\Merchant\Issue\Http\Resources\MerchantIssueTypeResource;
use App\Models\Merchant\MerchantIssueType;
use App\Services\ApiResponse;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class IssueTypeController extends Controller
{
    public function index(): JsonResponse
    {
        $types = MerchantIssueType::query()->where('is_active', true)->get();

        return ApiResponse::success(
            'Issue types retrieved successfully',
            MerchantIssueTypeResource::collection($types),
            Response::HTTP_OK
        );
    }
}
