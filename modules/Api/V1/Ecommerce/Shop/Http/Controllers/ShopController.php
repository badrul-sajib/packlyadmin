<?php

namespace Modules\Api\V1\Ecommerce\Shop\Http\Controllers;

use App\Actions\FetchAllShop;
use App\Actions\FetchPopularShops;
use App\Actions\FetchShopBasicDetails;
use App\Actions\FetchShopProducts;
use App\Actions\FetchShopReels;
use App\Actions\FollowShop;
use App\Caches\ShopSettingsCache;
use App\Http\Controllers\Controller;
use App\Http\Resources\Ecommerce\ProductsResource;
use App\Http\Resources\Ecommerce\ShopResource;
use App\Http\Resources\Merchant\NearByShopResource;
use App\Models\Coupon\Coupon;
use App\Models\Coupon\CouponMerchant;
use App\Models\Merchant\Merchant;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ShopController extends Controller
{
    public function basicDetails($id)
    {
        return (new FetchShopBasicDetails)->execute($id);
    }

    public function shopProducts(Request $request, $id)
    {
        try {
            $products = (new FetchShopProducts)->execute($request, $id);

            $data = ProductsResource::collection($products->items());

            return resourceFormatPagination('All products of this shop fetched successfully', $data, $products);
        } catch (\Exception $e) {
            Log::error('Error fetching shop products: '.$e->getMessage());

            return failure('Failed to fetch shop products', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function follow(Request $request, $id)
    {
        return (new FollowShop)->execute($request, $id);
    }

    public function popularShops(Request $request)
    {
        return (new FetchPopularShops)->execute($request);
    }

    public function shopReels()
    {
        return (new FetchShopReels)->execute();
    }

    public function shopCoupon($slug)
    {
        $merchant = Merchant::where('slug', $slug)->where('shop_status', 1)->first();
        $temp = [];

        if ($merchant) {
            $couponMerchant = CouponMerchant::where('merchant_id', $merchant->id)->get();
            if ($couponMerchant) {
                foreach ($couponMerchant as $data) {
                    $coupon = Coupon::where('id', $data->coupon_id)
                        ->whereDate('start_date', '<=', Carbon::now())
                        ->whereDate('end_date', '>=', Carbon::now())
                        ->first();
                    if ($coupon) {
                        $temp[] = $coupon;
                    }
                }

                return response()->json([
                    'data' => $temp,
                ]);
            }

            return response()->json([
                'data' => 'No Coupon Found',
            ]);
        }

        return response()->json([
            'data' => 'No Coupon Found',
        ]);
    }

    public function allShops(Request $request)
    {
        $shops = (new FetchAllShop)->execute($request);

        return formatPagination('All Shops fetched successfully', ShopResource::collection($shops));
    }

    public function nearbyShops(Request $request)
    {
        $latitude = $request->latitude ?? '';
        $longitude = $request->longitude ?? '';
        $radius = intval(ShopSettingsCache::findByKey('nearby_shop_radius_km') ?? 1);

        if (! $latitude || ! $longitude) {
            return failure('Latitude and longitude are required.');
        }

        $merchants = Merchant::select('*')
            ->selectRaw('
                (
                    6371 * acos(
                        cos(radians(?)) *
                        cos(radians(latitude)) *
                        cos(radians(longitude) - radians(?)) +
                        sin(radians(?)) *
                        sin(radians(latitude))
                    )
                ) AS distance
            ', [$latitude, $longitude, $latitude])
            ->having('distance', '<=', $radius)
            ->orderBy('distance')
            ->get();

        return success(
            'Nearby shops fetched successfully',
            NearByShopResource::collection($merchants)
        );
    }
}
