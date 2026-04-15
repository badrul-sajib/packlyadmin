<?php

namespace Modules\Api\V1\Ecommerce\Wishlist\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Api\V1\Ecommerce\Wishlist\Http\Resources\WishlistResource;
use App\Services\WishListService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Response;

class WishlistController extends Controller
{
    protected WishListService $wishlistService;

    public function __construct(WishListService $wishlistService)
    {
        $this->wishlistService = $wishlistService;
    }

    public function index()
    {
        try {
            $wishlistItems = $this->wishlistService->getUserWishlist();

            return success('Wishlist retrieved successfully', WishlistResource::collection($wishlistItems));
        } catch (\Throwable $th) {
            return failure($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function toggle($productId)
    {
        try {
            $result = $this->wishlistService->toggleWishlistItem(auth()->id(), $productId);

            return success(
                $result['message'],
                $result['data']
            );
        } catch (ModelNotFoundException) {
            return failure('Product not found', Response::HTTP_NOT_FOUND);
        }
    }
}
