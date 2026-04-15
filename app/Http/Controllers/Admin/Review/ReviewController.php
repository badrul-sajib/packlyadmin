<?php

namespace App\Http\Controllers\Admin\Review;

use App\Actions\ReviewDetails;
use App\Actions\ReviewList;
use App\Enums\ReviewStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Review\UpdateReviewRequest;
use App\Models\Review\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Activitylog\Models\Activity;
use Throwable;

class ReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:customer-product-review-list')->only('index');
        $this->middleware('permission:customer-product-review-show')->only('showReviews');
        $this->middleware('permission:customer-product-review-update')->only('edit', 'update', 'reviewApprove');
        $this->middleware('permission:customer-product-review-delete')->only('destroy');
    }

    /**
     * @throws Throwable
     */
    public function index()
    {
        $reviews = (new ReviewList)->handle();
        if (request()->ajax()) {
            return view('components.review.table', ['entity' => $reviews])->render();
        }

        return view('Admin::review.index', compact('reviews'));
    }

    public function showReviews(string $id)
    {
        $review = (new ReviewDetails)->handle($id);

        return view('Admin::review.show', compact('review'));
    }

    public function edit(string $id)
    {
        $review     = Review::findOrFail($id);
        $activities = Activity::where('subject_type', Review::class)
            ->where('subject_id', $review->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('Admin::review.edit', compact('review', 'activities'));
    }

    public function update(UpdateReviewRequest $request, string $id): RedirectResponse
    {
        DB::beginTransaction();

        try {
            $review = Review::findOrFail($id);

            $validated = $request->validated();

            $original = $review->only(['review', 'rating', 'seller_rating', 'shipping_rating']);

            $review->review          = $validated['review'];
            $review->rating          = $validated['rating'];
            $review->seller_rating   = $validated['seller_rating']   ?? null;
            $review->shipping_rating = $validated['shipping_rating'] ?? null;
            $review->save();

            $review->product->updateRating();

            activity()
                ->performedOn($review)
                ->causedBy(auth('admin')->user())
                ->withProperties([
                    'old' => $original,
                    'new' => $review->only(['review', 'rating', 'seller_rating', 'shipping_rating']),
                ])
                ->event('review-updated-by-admin')
                ->log('Admin updated review and ratings');
            DB::commit();

            return to_route('admin.reviews.index')->with('success', 'Review updated successfully');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th->getMessage());

            return to_route('admin.reviews.index')->with('error', 'Something went wrong');
        }
    }

    public function reviewApprove(string $id): RedirectResponse
    {
        $review              = Review::findOrFail($id);
        $review->is_approved = ReviewStatus::IS_APPROVED;
        $review->is_public   = ReviewStatus::IS_PUBLIC;
        $review->save();

        return back()->with('success', 'Review approved successfully');
    }

    public function destroy(string $id): RedirectResponse
    {
        $review = Review::findOrFail($id);
        $review->delete();

        return back()->with('success', 'Review deleted successfully');
    }
}
