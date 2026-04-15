<?php

namespace Modules\Api\V1\Merchant\Brand\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Brand\Brand;
use App\Models\Merchant\MerchantBrand;
use App\Services\ApiResponse;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class BrandController extends Controller
{
    public function __construct()
    {
        $this->middleware('shop.permission:show-brand')->only('index');
        $this->middleware('shop.permission:change-brand-status')->only('status');
    }
    /*
     * Lists all brands.
     */
    public function index(Request $request): JsonResponse
    {

        $brands = Brand::where(function ($query) {
            $query->where('brands.merchant_id', Auth::user()->merchant->id)
                ->where('brands.status', 1)
                ->orWhere('brands.merchant_id', null);
        })
            ->leftJoin('merchant_brands', function ($join) {
                $join->on('brands.id', '=', 'merchant_brands.brand_id')
                    ->where('merchant_brands.merchant_id', Auth::user()->merchant->id)
                    ->orWhereNull('merchant_brands.merchant_id');
            })
            ->select([
                'brands.id',
                'brands.name',
                'brands.slug',
                DB::raw('COALESCE(merchant_brands.status, 1) as status'),
            ])
            ->orderBy('brands.id', 'desc');

        if ($request->has('get_all') && $request->get('get_all')) {
            return ApiResponse::success('All Brands retrieved successfully', $brands->get(), Response::HTTP_OK);
        }

        return ApiResponse::formatPagination(
            'Brands retrieved successfully',
            $brands->paginate($request->query('per_page', 10)),
            Response::HTTP_OK
        );
    }

    /*
     * Updates status of a brand.
     */
    public function status(int $id): JsonResponse
    {
        try {
            $brand = Brand::findOrFail($id);

            $merchantBrand = MerchantBrand::firstOrCreate(
                [
                    'brand_id'    => $brand->id,
                    'merchant_id' => Auth::user()->merchant->id,
                ],
                [
                    'status' => 1,
                ]
            );
            // Toggle status using boolean to prevent type mismatch
            $newStatus = $merchantBrand->status == 1 ? 0 : 1;

            // Update only merchant_brand status
            $merchantBrand->update([
                'status' => $newStatus,

            ]);

            return ApiResponse::successMessageForCreate('Brand status updated successfully.', Response::HTTP_OK);
        } catch (ModelNotFoundException) {
            return ApiResponse::failure('Brand not found.', Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return ApiResponse::failure('Brand not found.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
