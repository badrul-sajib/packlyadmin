<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Services\ProductCommentService;
use Throwable;

class ProductCommentController extends Controller
{
    public function __construct(private readonly ProductCommentService $productCommentService)
    {
        $this->middleware('permission:product-question-list', ['only' => ['index']]);
    }

    /**
     * @throws Throwable
     */
    public function index()
    {
        $comments = $this->productCommentService->productCommentList();
        if (request()->ajax()) {
            return view('components.product_comment.table', ['entity' => $comments])->render();
        }

        return view('Admin::product_comment.index', compact('comments'));
    }
}
