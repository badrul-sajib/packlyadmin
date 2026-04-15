<?php

namespace App\Services;

use App\Models\Product\ProductComment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Throwable;

class MerchantProductCommentService
{
    public function productCommentList(): LengthAwarePaginator|array|\Illuminate\Pagination\LengthAwarePaginator
    {
        $search    = request('search', '');
        $perPage   = request('perPage', 10);
        $page      = request('page', 1);
        $type      = request('type', 1);
        $productId = request('product_id');

        return ProductComment::with([
            'user.media',
            'product:id,name,merchant_id,slug',
            'product.media',
            'merchant',
        ])
            ->where('merchant_id', auth()->user()->merchant->id)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('product', function ($subQuery) use ($search) {
                        $subQuery->where('name', 'like', "%{$search}%");
                    });
                });
            })
            ->when($productId, function ($query) use ($productId) {
                $query->where('product_id', $productId);
            })
            ->when($type, function ($query) use ($type) {
                if ($type == '1') {
                    $query->whereNull('reply');
                } elseif ($type == '2') {
                    $query->whereNotNull('reply');
                }
            })
            ->latest()->paginate($perPage, ['*'], 'page', $page)->withQueryString();
    }

    /**
     * @throws Throwable
     */
    public function productGetById(int $id): ProductComment
    {
        $ProductComment = ProductComment::where('id', $id)
            ->where('merchant_id', auth()->user()->merchant->id)
            ->first();
        throw_if(! $ProductComment, ModelNotFoundException::class, 'ProductComment not found');

        return $ProductComment;
    }

    /**
     * @throws Throwable
     */
    public function productCommentReply($reply, int $id): ProductComment
    {
        $comment        = $this->productGetById($id);
        $comment->reply = $reply;
        $comment->save();

        return $comment;
    }

    /**
     * @throws Throwable
     */
    public function productCommentDelete(int $id): ProductComment
    {
        $comment = $this->productGetById($id);
        $comment->delete();

        return $comment;
    }
}
