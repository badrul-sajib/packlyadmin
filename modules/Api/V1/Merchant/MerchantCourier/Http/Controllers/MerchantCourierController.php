<?php

namespace Modules\Api\V1\Merchant\MerchantCourier\Http\Controllers;

use App\Enums\MerchantStatus;
use App\Http\Controllers\Controller;
use Modules\Api\V1\Merchant\MerchantCourier\Http\Requests\MerchantCourierRequest;
use App\Services\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class MerchantCourierController extends Controller
{
    public function __construct()
    {
        $this->middleware('shop.permission:show-merchant-couriers')->only('index');
        $this->middleware('shop.permission:create-merchant-courier')->only('store');
        $this->middleware('shop.permission:update-merchant-courier')->only('update');
        $this->middleware('shop.permission:delete-merchant-courier')->only('detachCourier');
    }
    /**
     * Get all couriers for a specific merchant.
     */
    public function index(Request $request): JsonResponse
    {
        $merchant = Auth::user()->merchant->load('couriers');

        try {
            $couriers = $merchant->couriers()->orderBy('id', 'desc')->paginate($request->query('per_page', 10));

            return ApiResponse::formatPagination('Couriers retrieved successfully', $couriers, Response::HTTP_OK);
        } catch (Exception $e) {
            return ApiResponse::failure('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Attach a courier to a merchant.
     */
    public function store(MerchantCourierRequest $request): JsonResponse
    {
        // check merchant status
        if (Auth::user()->merchant->shop_status != MerchantStatus::Active) { // 1 for active
            return ApiResponse::failure('Your merchant account is not active.', Response::HTTP_FORBIDDEN);
        }

        try {
            $request->validated();

            $merchant = Auth::user()->merchant;

            // If the new courier is set as default, reset all others to is_default = 0
            if ($request->is_default == 1) {
                $merchant->couriers()->updateExistingPivot($merchant->couriers()->pluck('couriers.id')->toArray(), ['is_default' => '0']);
            }

            // Attach or update existing pivot record
            $data = [
                'base_url'     => env('STEADFAST_BASE_URL', 'https://portal.packzy.com/api/v1'),
                'api_key'      => $request->api_key,
                'secret_key'   => $request->secret_key,
                'is_default'   => $request->is_default,
                'is_active'    => $request->is_active,
                'auth_token'   => Str::random(32),
                'callback_url' => url('/api/v1/merchant/' . $merchant->uuid . '/callback'),
            ];

            $merchant->couriers()->syncWithoutDetaching([
                $request->courier_id => $data,
            ]);

            return ApiResponse::success('Courier attached successfully', $data, Response::HTTP_OK);
        } catch (Exception $e) {
            return ApiResponse::failure('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(MerchantCourierRequest $request, int $id): JsonResponse
    {
        try {
            $request->validated();

            $merchant = Auth::user()->merchant;

            // Attach or update existing pivot record
            $data = [
                'base_url'     => env('STEADFAST_BASE_URL', 'https://portal.packzy.com/api/v1'),
                'api_key'      => $request->api_key,
                'secret_key'   => $request->secret_key,
                'is_default'   => "$request->is_default",
                'is_active'    => "$request->is_active",
                'auth_token'   => $request->auth_token,
                'callback_url' => $request->callback_url,
            ];

            $merchant->couriers()->syncWithoutDetaching([
                $id => $data,
            ]);

            return ApiResponse::success('Courier updated successfully', $data, Response::HTTP_OK);
        } catch (Exception $e) {
            return ApiResponse::failure('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Detach a courier from a merchant.
     */
    public function detachCourier(int $id): JsonResponse
    {
        $merchant = Auth::user()->merchant;

        $merchant->couriers()->wherePivot('courier_id', $id)->detach($id);

        return ApiResponse::success('Courier removed from merchant successfully', [], Response::HTTP_OK);
    }
}
