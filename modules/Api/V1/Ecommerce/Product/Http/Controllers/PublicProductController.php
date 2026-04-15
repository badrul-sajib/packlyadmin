<?php

namespace Modules\Api\V1\Ecommerce\Product\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\ApiResponse;
use Modules\Api\V1\Ecommerce\Product\Http\Resources\PublicShopProductResource;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;
use App\Traits\VisitorTrackable;

class PublicProductController extends Controller
{
    use VisitorTrackable;
    public function __construct(protected ProductService $productService) {}

    public function index(Request $request): JsonResponse
    {
        try {
            if ($response = $this->handleVisitor($request)) {

                return $response;
            }
            $publicPassword = env('PUBLIC_API_PASSWORD');

            $deviceId = env('DEVICE_ID');
            $headerAccessKey = $request->header('accessKey');
            $deviceKeyHeader = $request->header('deviceId');
            $signatureHeader = $request->header('signature');
            if (!$headerAccessKey || !$deviceKeyHeader || !$signatureHeader) {
                return ApiResponse::error('An error occurred while fetching attributes.', Response::HTTP_FORBIDDEN);
            }
            if ($deviceId !== $deviceKeyHeader) {
                return ApiResponse::error('An error occurred while fetching attributes.', Response::HTTP_FORBIDDEN);
            }
            $expectedSignature = md5($publicPassword . $deviceId);
            if (!hash_equals($expectedSignature, $signatureHeader)) {
                return ApiResponse::error('An error occurred while fetching attributes.', Response::HTTP_FORBIDDEN);
            }
            if ($headerAccessKey !== $publicPassword || $deviceKeyHeader !== $deviceId) {
                return ApiResponse::error('An error occurred while fetching attributes.', Response::HTTP_FORBIDDEN);
            }

            $products = $this->productService->getNewShopProducts($request);
            $isCursor = $request->get('is-cursor', false);

            if ($isCursor) {
                $data = response()->json(
                    [
                        'message' => 'Public shop products fetched successfully',
                        'data' => PublicShopProductResource::collection($products->items()),
                        'total' => $products->total_count ?? null,
                        'last_page' => null,
                        'current_page' => $request->get('cursor'),
                        'next_page_url' => $products->nextCursor()?->encode(),
                        'prev_page_url' => $products->previousCursor()?->encode(),
                    ],
                    200,
                );
            } else {
                $data = response()->json(
                    [
                        'message' => 'Public shop products fetched successfully',
                        'data' => PublicShopProductResource::collection($products->items()),
                        'total' => $products->total(),
                        'last_page' => $products->lastPage(),
                        'current_page' => $products->currentPage(),
                        'next_page_url' => $products->nextPageUrl(),
                    ],
                    200,
                );
            }
            return $data;
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Something went wrong'], 500);
        }
    }
}
