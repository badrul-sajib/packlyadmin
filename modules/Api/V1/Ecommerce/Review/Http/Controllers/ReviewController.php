<?php

namespace Modules\Api\V1\Ecommerce\Review\Http\Controllers;

use Exception;
use Throwable;
use Illuminate\Http\Request;
use App\Models\Review\Review;
use App\Services\ReviewService;
use App\Models\Merchant\Merchant;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Actions\FetchProductReview;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\ReviewExpireException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Api\V1\Ecommerce\Review\Http\Requests\ReviewRequest;
use Modules\Api\V1\Ecommerce\Review\Http\Requests\ReviewUpdateRequest;
use Modules\Api\V1\Ecommerce\Review\Http\Resources\ReviewDetailsResource;

class ReviewController extends Controller
{
    public function __construct(private readonly ReviewService $reviewService) {}

    /**
     * @throws Throwable
     */
    public function store(ReviewRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $res = $this->reviewService->store($request);
            DB::commit();

            return success('Review created successfully', $res);
        } catch (Exception $e) {
            DB::rollBack();

            return failure('Failed to update review', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(Review $review): JsonResponse
    {
        if ($review->user_id  != Auth::user()->id) {
            return failure('Invalid review', Response::HTTP_NOT_FOUND);
        }

        return success('Review fetched successfully', new ReviewDetailsResource($review));
    }

    public function update(ReviewUpdateRequest $request, Review $review): JsonResponse
    {
        $data = $request->validated();
        DB::beginTransaction();

        try {
            $response = $this->reviewService->updateReview($data, $review);
            DB::commit();

            return success('Review updated successfully', $response);
        } catch (ReviewExpireException $e) {
            DB::rollBack();
            return failure($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();

            return validationError('Model not found', 'Review not found', Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            DB::rollBack();

            return failure('Failed to update review.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function ProductReviews(Request $request, $slug)
    {
        return (new FetchProductReview)->execute($request, $slug);
    }

    public function myReviews()
    {
        return $this->reviewService->getReviews();
    }

    public function toReviews()
    {
        return $this->reviewService->getToReviews();
    }

    public function shopReviews($id)
    {
        try {
            $merchant = Merchant::Active()->where('id', $id)->firstOrFail();

            return $this->reviewService->getShopReviews($merchant);
        } catch (ModelNotFoundException) {
            return failure('Merchant not found', 404);
        }
    }
}
