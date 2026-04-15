<?php

namespace Modules\Api\V1\Merchant\Courier\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Courier\Courier;
use App\Services\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CourierController extends Controller
{
    public function __construct()
    {
        $this->middleware('shop.permission:show-couriers')->only('index');
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $couriers = Courier::orderBy('id', 'desc')
                ->paginate($request->query('per_page', 10));

            return ApiResponse::formatPagination('Couriers retrieved successfully', $couriers, Response::HTTP_OK);
        } catch (Exception $e) {
            return ApiResponse::failure('Courier not found.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
