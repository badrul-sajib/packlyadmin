<?php

namespace Modules\Api\V1\Merchant\Campaign\Http\Controllers;

use App\Enums\CampaignProductStatus;
use App\Enums\ShopProductStatus;
use App\Http\Controllers\Controller;
use App\Models\Campaign\Campaign;
use App\Models\Campaign\CampaignProduct;
use App\Models\Product\Product;
use App\Services\ApiResponse;
use App\Traits\ProductCommission;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Modules\Api\V1\Merchant\Campaign\Http\Requests\CampaignProductRequest;
use Modules\Api\V1\Merchant\Campaign\Http\Requests\CampaignProductSearchRequest;
use Modules\Api\V1\Merchant\Campaign\Http\Resources\CampaignProductResource;
use Modules\Api\V1\Merchant\Campaign\Http\Resources\CampaignResource;
use Modules\Api\V1\Merchant\Campaign\Services\CampaignService;
use Symfony\Component\HttpFoundation\Response;

class CampaignController extends Controller
{
    use ProductCommission;

    public function __construct(
        protected CampaignService $service
    ) {
    }

    public function index(): JsonResponse
    {
        try {
            $data = $this->service->activeAll();

            return ApiResponse::success(
                'Campaigns retrieved successfully',
                CampaignResource::collection($data)
            );
        } catch (\Throwable $e) {
            return ApiResponse::error('Campaigns not found', 404);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $item = $this->service->find($id);

            return ApiResponse::success(
                'Campaign retrieved',
                new CampaignResource($item)
            );
        } catch (\Throwable $e) {
            return ApiResponse::error('Campaign not found', 404);
        }
    }

    public function search(CampaignProductSearchRequest $request): JsonResponse
    {
        try {
            $merchant = $request->user()->merchant;

            // Exclude products already in campaign (pending or approved)
            $excludedProductIds = CampaignProduct::where('campaign_id', $request->campaign_id)
                ->where('merchant_id', $merchant->id)
                ->whereIn('status', [
                    CampaignProductStatus::PENDING->value,
                    CampaignProductStatus::APPROVED->value
                ])
                ->pluck('product_id')
                ->toArray();

            $search = $request->input('search');
            $productId = $request->input('product_id');
            $categoryId = $request->query('category_id');
            $productType = $request->query('product_type');
            $sortOrder = in_array($request->query('sort_order'), ['asc', 'desc'])
                ? $request->query('sort_order')
                : 'desc';
            $perPage = $request->query('per_page', 10);

            $query = Product::query()
                ->where('merchant_id', $merchant->id)
                ->where('status', 1)
                ->where('total_stock_qty', '>', 0)
                ->whereNotIn('id', $excludedProductIds)

                // 🔒 HARD RULE: product MUST have approved & active shopProducts
                ->whereHas('shopProducts', function ($q) {
                    $q->where('status', ShopProductStatus::APPROVED->value)
                        ->where('active_status', 1);
                });

            $query->with([
                'category',
                'productDetail',
                'shopProducts',
                'variations' => function ($q) {
                    $q->where('status', 1)
                        ->with(['variationAttributes.attribute.options']);
                }
            ])
                ->withCount([
                    'variations' => function ($q) {
                        $q->where('status', 1);
                    }
                ])

                ->when($productId, fn($q) => $q->where('id', $productId))

                ->when($search, function ($q) use ($search) {
                    $q->where(function ($sub) use ($search) {
                        $sub->where('name', 'LIKE', "%{$search}%")
                            ->orWhere('sku', 'LIKE', "%{$search}%");
                    });
                })

                ->when($categoryId, fn($q) => $q->where('category_id', $categoryId))

                ->when(in_array($productType, [1, 2]), fn($q) => $q->where('product_type', $productType))

                ->orderBy('id', $sortOrder);

            $results = $query->paginate($perPage);

            // Format response
            $formattedResults = $results->through(function ($product) {

                $commissionData = $this->getProductCommission($product);
                $product->commission      = $commissionData->commission;
                $product->commission_type = $commissionData->commission_type;

                $product->variations = $product->variations->map(function ($variation) {
                    $variation->attributes = $variation->variationAttributes->map(function ($attribute) {
                        return [
                            'name' => $attribute->attribute->name,
                            'value' => $attribute->attribute->options
                                ->firstWhere('id', $attribute->attribute_option_id)
                                ->attribute_value ?? null,
                        ];
                    });

                    unset($variation->variationAttributes);
                    return $variation;
                });

                return $product;
            });

            return ApiResponse::formatPagination(
                'Search results',
                $formattedResults,
                Response::HTTP_OK
            );

        } catch (Exception $e) {
            return ApiResponse::failure(
                'Something went wrong',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function requestProducts(CampaignProductRequest $request): JsonResponse
    {
        $campaign = Campaign::find($request->campaign_id);

        $start = Carbon::parse($campaign->vendor_request_start);
        $end = Carbon::parse($campaign->vendor_request_end);
        $now = Carbon::now();

        if (!$now->between($start, $end)) {
            return ApiResponse::error(
                'Campaign is not active yet',
                Response::HTTP_CONFLICT
            );
        }

        DB::beginTransaction();
        try {
            foreach ($request->products as $product) {
                $campaignProduct = CampaignProduct::where(['campaign_id' => $request->campaign_id, 'merchant_id' => $request->user()->merchant->id, 'product_id' => $product])->whereIn('status', [CampaignProductStatus::PENDING->value, CampaignProductStatus::APPROVED->value])->first();
                if ($campaignProduct) {
                    return ApiResponse::error(
                        $campaignProduct->product->name . ' is already requested',
                        Response::HTTP_CONFLICT
                    );
                }

                CampaignProduct::create([
                    'merchant_id' => $request->user()->merchant->id,
                    'campaign_id' => $request->campaign_id,
                    'prime_view_id' => $request->prime_view_id,
                    'product_id' => $product,
                    'status' => CampaignProductStatus::PENDING->value,
                ]);
            }
            DB::commit();

            return ApiResponse::success(
                'Products requested successfully',
                [],
                Response::HTTP_OK
            );

        } catch (Exception $e) {
            DB::rollBack();

            return ApiResponse::error(
                'Failed to request products',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function registerCampaigns(): JsonResponse
    {
        $merchantId = request()->user()->merchant->id;

        // Get distinct campaign IDs for this merchant
        $campaignProducts = CampaignProduct::where('merchant_id', $merchantId)
            ->with('campaign')
            ->get()
            ->groupBy('campaign_id')
            ->map(function ($group) {
                return $group->first()->campaign; // return Campaign model
            })
            ->values(); // reset array keys

        return ApiResponse::success(
            'Campaign registered successfully',
            CampaignResource::collection($campaignProducts),
            Response::HTTP_OK
        );
    }

    public function getRequestProducts(CampaignProductSearchRequest $request): JsonResponse
    {
        $merchantId = request()->user()->merchant->id;
        
        $products = CampaignProduct::where(['merchant_id' => $merchantId])->when($request->prime_view_id, fn($q) => $q->where('prime_view_id', $request->prime_view_id))->when($request->campaign_id, fn($q) => $q->where('campaign_id', $request->campaign_id))->get();

        return ApiResponse::success(
            'Products retrieved successfully',
            CampaignProductResource::collection($products),
            Response::HTTP_OK
        );
    }
}
