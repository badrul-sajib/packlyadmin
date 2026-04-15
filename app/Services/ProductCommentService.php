<?php

namespace App\Services;

use App\Models\Product\Product;
use App\Models\Product\ProductComment;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProductCommentService
{
    public function productCommentList()
    {
        $search       = request('search', '');
        $perPage      = request('perPage', 10);
        $page         = request('page', 1);
        $productId    = request('product_id');
        $merchantId   = request('merchant_id');

        return ProductComment::with([
            'user.media',
            'product:id,name,merchant_id,slug',
            'product.media',
            'merchant',
        ])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('product', function ($subQuery) use ($search) {
                        $subQuery->where('name', 'like', "%{$search}%");
                    });
                });
            })
            ->when($merchantId, function ($query) use ($merchantId) {
                $query->where('merchant_id', $merchantId);
            })
            ->when($productId, function ($query) use ($productId) {
                $query->where('product_id', $productId);
            })
            ->latest()->paginate($perPage, ['*'], 'page', $page)->withQueryString();
    }

    public function productComments($slug)
    {
        $per_page = request('per_page') ?? 10;
        $product  = $this->productGetBySlug($slug);

        // model not found
        throw_if(! $product, ModelNotFoundException::class, 'Product not found');

        return ProductComment::query()
            ->where('product_id', $product->id)
            ->whereNotNull('reply')
            ->when(auth()->user(), function ($query) {
                $query->where('user_id', '!=', auth()->user()->id);
            })
            ->with('merchant', 'user')
            ->orderBy('created_at', 'desc')
            ->paginate($per_page);
    }

    /**
     * @throws Throwable
     */
    public function productMyComments($slug)
    {
        $per_page = request('per_page') ?? 10;
        $product  = $this->productGetBySlug($slug);

        // model not found
        throw_if(! $product, ModelNotFoundException::class, 'Product not found');

        return ProductComment::query()
            ->where('user_id', auth()->user()->id)
            ->where('product_id', $product->id)
            ->with('merchant', 'user')
            ->orderBy('created_at', 'desc')
            ->paginate($per_page);
    }

    /**
     * @throws Throwable
     */
    public function productGetBySlug($slug): Product
    {
        $product = Product::where('slug', $slug)->first();
        throw_if(! $product, ModelNotFoundException::class, 'Product not found');

        return $product;
    }

    /**
     * @throws Throwable
     */
    public function productCommentStore($request, $slug)
    {
        $product = $this->productGetBySlug($slug);

        $productComment = ProductComment::create([
            'product_id'  => $product->id,
            'merchant_id' => $product->merchant_id,
            'user_id'     => auth()->user()->id,
            'comment'     => $request->comment,
        ]);

        try {

            $userName     = $productComent->user->name ?? 'Customer';
            $notification = "Hi {$productComent->merchant->name}, You have a comment from {$userName}";
            $productComent->merchant->sendNotification('New Order', $notification, '/product-comments');
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }

        return $productComment;
    }
}
