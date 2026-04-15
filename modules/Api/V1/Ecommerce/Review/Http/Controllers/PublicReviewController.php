<?php

namespace Modules\Api\V1\Ecommerce\Review\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Review\Review;
use App\Services\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Traits\VisitorTrackable;

class PublicReviewController extends Controller
{
    use VisitorTrackable;
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
            $perPage    = (int) ($request->input('per_page', 20));
            $productId  = $request->input('product_id');
            $userId     = $request->input('user_id');
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

            $reviews = Review::query()
                ->select(['user_id', 'product_id', 'rating', 'created_at'])
                ->when($productId, function ($q) use ($productId) {
                    $q->where('product_id', $productId);
                })
                ->when($userId, function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                })
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            $items = $reviews->getCollection()->map(function ($r) {
                return [
                    'user_id'    => (int) $r->user_id,
                    'product_id' => (int) $r->product_id,
                    'rating'     => (float) $r->rating,
                    'timestamp'  => $r->created_at->toDateTimeString(),
                ];
            });

            return response()->json([
                'message'       => 'Ratings fetched successfully',
                'data'          => $items,
                'total'         => $reviews->total(),
                'last_page'     => $reviews->lastPage(),
                'current_page'  => $reviews->currentPage(),
                'next_page_url' => $reviews->nextPageUrl(),
            ]);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Something went wrong'], 500);
        }
    }
}
