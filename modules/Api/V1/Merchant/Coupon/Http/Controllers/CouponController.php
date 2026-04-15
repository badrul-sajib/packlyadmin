<?php

namespace Modules\Api\V1\Merchant\Coupon\Http\Controllers;

use App\Enums\MerchantStatus;
use App\Http\Controllers\Controller;
use Modules\Api\V1\Merchant\Coupon\Http\Requests\CouponRequest;
use App\Services\ApiResponse;
use App\Services\CouponService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class CouponController extends Controller
{

    protected CouponService $couponService;

    public function __construct(CouponService $couponService)
    {
        $this->couponService = $couponService;
        $this->middleware('shop.permission:show-coupons')->only('index', 'show');
        $this->middleware('shop.permission:create-coupon')->only('store');
        $this->middleware('shop.permission:update-coupon')->only('update');
        $this->middleware('shop.permission:delete-coupon')->only('destroy');
    }

    /*
     * Lists all coupons.
     */
    public function index(Request $request): JsonResponse
    {
        $coupons = $this->couponService->getAllCoupons($request);

        return ApiResponse::success('Coupons retrieved successfully', $coupons, Response::HTTP_OK);
    }

    /**
     * Creates a new coupon.
     *
     * @throws Throwable
     */
    public function store(CouponRequest $request): JsonResponse
    {
        // check merchant status
        if (Auth::user()->merchant->shop_status != MerchantStatus::Active) { // 1 for active
            return ApiResponse::failure('Your merchant account is not active.', Response::HTTP_FORBIDDEN);
        }

        $merchantId = $request->user()->merchant->id;

        $request->merge([
            'merchant_id' => $merchantId,
        ]);

        return $this->couponService->createCoupon($request);
    }

    /*
     * Gets a coupon by ID.
     */
    public function show(int $id): JsonResponse
    {
        $coupon = $this->couponService->getCouponById($id);

        return $coupon
            ? ApiResponse::success('Coupon retrieved successfully', $coupon, Response::HTTP_OK)
            : ApiResponse::failure('Coupon not found', Response::HTTP_NOT_FOUND);
    }

    /**
     * Updates a coupon.
     *
     * @throws Throwable
     */
    public function update(CouponRequest $request, int $id): JsonResponse
    {
        $merchantId = $request->user()->merchant->id;

        $request->merge([
            'merchant_id' => $merchantId,
        ]);

        $coupon = $this->couponService->updateCoupon($request, $id);

        return ApiResponse::success('Coupon updated successfully', $coupon, Response::HTTP_OK);
    }

    /*
     * Deletes a coupon.
     */
    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->couponService->deleteCoupon($id);

        return $deleted
            ? ApiResponse::success('Coupon deleted successfully', [], Response::HTTP_OK)
            : ApiResponse::failure('Coupon not found', Response::HTTP_NOT_FOUND);
    }
}
