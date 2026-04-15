<?php

namespace Modules\Api\V1\Ecommerce\Product\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Api\V1\Ecommerce\Product\Http\Requests\ProductCommentStoreRequest;
use App\Http\Resources\Ecommerce\ProductCommentList;
use App\Services\ProductCommentService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductCommentController extends Controller
{
    public function __construct(private readonly ProductCommentService $productCommentService) {}

    public function productComments(string $slug)
    {
        try {

            $comments = $this->productCommentService->productComments($slug);
            $items    = ProductCommentList::collection($comments);

            return resourceFormatPagination('Show all product comments', $items, $comments, 200);
        } catch (ModelNotFoundException) {
            return failure('Product not found', 404);
        } catch (Exception $e) {
            return failure('Product not found', 500);
        }
    }

    public function productMyComments(string $slug)
    {
        try {
            $comments = $this->productCommentService->productMyComments($slug);
            $items    = ProductCommentList::collection($comments);

            return resourceFormatPagination('Show all product comments', $items, $comments, 200);
        } catch (ModelNotFoundException) {
            return failure('Product not found', 404);
        } catch (Exception $e) {
            return failure('Product not found', 500);
        }
    }

    public function productCommentStore(ProductCommentStoreRequest $request, $slug)
    {
        $request->validated();

        try {
            $comment = $this->productCommentService->productCommentStore($request, $slug);

            return success('Question submitted successfully!', $comment, 200);
        } catch (ModelNotFoundException) {
            return failure('Product not found', 404);
        } catch (Exception $e) {
            return failure('Product not found', 500);
        }
    }
}
