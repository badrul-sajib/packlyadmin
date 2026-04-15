<?php

namespace Modules\Api\V1\Ecommerce\Voucher\Http\Controllers;

use App\Actions\FetchMerchantVouchar;
use App\Actions\FetchVouchar;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VoucharController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return (new FetchVouchar)->execute($request);
    }

    public function merchantVouchers(Request $request, $id): JsonResponse
    {
        return (new FetchMerchantVouchar)->execute($request, $id);

    }
}
