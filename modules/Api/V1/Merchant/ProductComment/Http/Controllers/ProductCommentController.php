<?php

namespace Modules\Api\V1\Merchant\ProductComment\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Api\V1\Merchant\ProductComment\Http\Requests\ProductCommentRequest;
use App\Services\ApiResponse;
use App\Services\MerchantProductCommentService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ProductCommentController extends Controller
{
    public function __construct(private readonly MerchantProductCommentService $productCommentService)
    {
        $this->middleware('shop.permission:show-product-comments')->only('index');
        $this->middleware('shop.permission:delete-product-comment')->only('delete');
        $this->middleware('shop.permission:reply-product-comment')->only('reply');
    }

    /*
     * Fetch a list of product comments
     */
    public function index(): JsonResponse
    {
        try {
            $comments = $this->productCommentService->productCommentList();

            return ApiResponse::formatPagination('All Product comments retrieved successfully', $comments, Response::HTTP_OK);
        } catch (ModelNotFoundException) {
            return ApiResponse::failure('Product not found', Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return ApiResponse::failure('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete(int $id): JsonResponse
    {
        try {
            $this->productCommentService->productCommentDelete($id);

            return ApiResponse::success('Product Comment deleted successfully', null, Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::failure('Product not found', Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return ApiResponse::failure('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /*
     * Submit a reply to a product comment
     */
    public function reply(ProductCommentRequest $request, int $id): JsonResponse
    {
        $request->validated();

        try {
            $comment = $this->productCommentService->productCommentReply($request->reply, $id);

            return ApiResponse::success('Answer submitted successfully!', $comment, Response::HTTP_OK);
        } catch (ModelNotFoundException) {
            return ApiResponse::failure('Product not found', Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return ApiResponse::failure('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
