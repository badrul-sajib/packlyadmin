<?php

namespace Modules\Api\V1\Merchant\PrimeView\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\ApiResponse;
use App\Services\PrimeViewService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class PrimeViewController extends Controller
{
    private PrimeViewService $primeViewService;

    public function __construct(PrimeViewService $primeViewService)
    {
        $this->primeViewService = $primeViewService;
    }

    public function index(): JsonResponse
    {
        $primeViews = $this->primeViewService->getAll();

        return ApiResponse::success('All PrimeViews retrieved successfully', $primeViews, Response::HTTP_OK);
    }
}
