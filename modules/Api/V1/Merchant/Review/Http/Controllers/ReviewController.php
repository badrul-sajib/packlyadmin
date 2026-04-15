<?php

namespace Modules\Api\V1\Merchant\Review\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Api\V1\Merchant\Review\Http\Requests\ReviewRequest;
use Modules\Api\V1\Merchant\Review\Http\Resources\ReviewResource;
use App\Services\ApiResponse;
use App\Services\MerchantReviewService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ReviewController extends Controller
{
    public function __construct(private readonly MerchantReviewService $reviewService)
    {
        $this->middleware('shop.permission:show-reviews')->only('index');
        $this->middleware('shop.permission:reply-review')->only('reply');
    }
    public function index(): JsonResponse
    {
        $reviews = $this->reviewService->getAllReviews();

        return ApiResponse::formatPagination('All Reviews retrieved successfully', ReviewResource::collection($reviews), Response::HTTP_OK);
    }

    public function reply(ReviewRequest $request, int $id): JsonResponse
    {
        $request->validated();

        try {
            $reply = $this->reviewService->replyStore($request->reply_message, $id);

            return ApiResponse::success('Review Replied', $reply, Response::HTTP_OK);
        } catch (\Throwable $th) {
            return ApiResponse::failure('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
