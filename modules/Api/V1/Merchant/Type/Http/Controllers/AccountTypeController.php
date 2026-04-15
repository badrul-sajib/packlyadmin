<?php

namespace Modules\Api\V1\Merchant\Type\Http\Controllers;

use App\Enums\AccountTypes;
use App\Http\Controllers\Controller;
use App\Services\ApiResponse;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AccountTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('shop.permission:show-account-type')->only('index');
    }
    /*
     * Lists all available account types.
     */
    public function index(): JsonResponse
    {
        $allList = [];

        foreach (AccountTypes::cases() as $type) {
            $allList[] = [
                'id'   => $type->value,
                'name' => $type->getValues(),
            ];
        }

        return ApiResponse::success('Account types retrieved successfully', $allList, Response::HTTP_OK);
    }
}
