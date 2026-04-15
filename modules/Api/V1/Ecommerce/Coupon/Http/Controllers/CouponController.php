<?php

namespace Modules\Api\V1\Ecommerce\Coupon\Http\Controllers;

use App\Caches\ShopSettingsCache;
use App\Enums\CouponApplyOn;
use App\Http\Controllers\Controller;
use App\Services\Coupon\CouponCheckoutValidator;
use App\Services\Coupon\CouponValidator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Modules\Api\V1\Ecommerce\Coupon\Http\Requests\CouponProductEligibilityRequest;
use Modules\Api\V1\Ecommerce\Coupon\Http\Requests\ShopCouponProductEligibilityRequest;

class CouponController extends Controller
{
    public function __construct(
        private readonly CouponValidator $couponValidator,
        private readonly CouponCheckoutValidator $couponCheckoutValidator,
    ) {}

    public function coupons($productId): JsonResponse
    {
        $coupons = $this->couponValidator->getProductIdByCoupons($productId);

        return success('Coupons fetched successfully', $coupons);
    }

    public function couponProductEligibility(CouponProductEligibilityRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $coupons = $this->couponValidator->getProductsByCoupons(
            $validated['product_ids'],
            $validated['coupon_ids']
        );

        return success('Coupons eligibility checked successfully', $coupons);

    }

    public function shopCouponProductEligibility(ShopCouponProductEligibilityRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $coupons = $this->couponCheckoutValidator->validate($data);

            if (! $coupons) {
                return failure('No coupons found');
            }

            if (
                isset($coupons['type']) && $coupons['bear_by_packly'] == 1
            ) {
                return response()->json([
                    'message' => 'Shop coupons eligibility checked successfully',
                    'bear_by_packly' => $coupons['bear_by_packly'],
                    'type' => $coupons['type'],
                    'discount_amount' => $coupons['discount_amount'],
                    'data' => [],
                ]);
            }

            return response()->json([
                'message' => 'Shop coupons eligibility checked successfully',
                'type' => CouponApplyOn::PRODUCT_PRICE->value,
                'discount_amount' => 0,
                'bear_by_packly' => 0,
                'data' => $coupons,
            ]);

        } catch (\Throwable $th) {
            return failure('Something went wrong.'.$th->getMessage());
        }
    }

    public function couponDefault(): JsonResponse
    {
        try {
            $coupons = ShopSettingsCache::findByKey('default_coupon_code');
            return success('Coupons fetched successfully', $coupons);
        } catch (\Throwable $th) {
            Log::error('Coupon default error: '.$th->getMessage());
            return failure('Something went wrong.');
        }
    }
}
