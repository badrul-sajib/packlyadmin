<?php

namespace App\Services;

use App\Models\PrimeView\PrimeView;
use App\Models\PrimeView\PrimeViewProduct;
use App\Models\Product\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PrimeViewProductService
{
    public function getProducts($request): LengthAwarePaginator
    {
        $perPage       = $request->input('perPage', 500);
        $page          = $request->input('page', 1);
        $search        = $request->input('search', '');
        $stock_status  = $request->input('stock_status', '');
        $prime_view_id = $request->input('prime_view_id', '');

        return PrimeViewProduct::query()
            ->with([
                'product',
                'product.media',
                'product.category:id,name',
                'product.productDetail',
                'prime_view',
            ])
            ->when($search, function ($query) use ($search) {
                $query->whereHas('product', function ($query) use ($search) {
                    return $query->where('name', 'like', '%'.$search.'%');
                });
            })
            ->when($prime_view_id, function ($query) use ($prime_view_id) {
                $query->where('prime_view_id', $prime_view_id);
            })
            ->when($stock_status, function ($query) use ($stock_status) {
                $query->whereHas('product', function ($query) use ($stock_status) {
                    if ($stock_status == '1') {
                        return $query->where('total_stock_qty', '>', 0);
                    }
                    if ($stock_status == '2') {
                        return $query->where('total_stock_qty', 0);
                    }
                    if ($stock_status == '3') {
                        return $query->where('total_stock_qty', '<', 10);
                    }
                });
            })
            ->orderBy('order')
            ->paginate($perPage, ['*'], 'page', $page);
    }

    public function storePrimeViewProduct($data): array|string
    {
        try {
            $primeView        = PrimeView::find($data['prime_view_id']);
            $existingProducts =
                $primeView->products()
                    ->whereIn('product_id', $data['products'])
                    ->pluck('product_id')->toArray();

            $newProducts = array_diff($data['products'], $existingProducts);

            if (! empty($newProducts)) {
                $primeView->products()->attach($newProducts);

                $productNames = Product::whereIn('id', $newProducts)
                    ->pluck('name')
                    ->toArray();

                activity()
                    ->useLog('product-add-in-prime-view')
                    ->event('created')
                    ->performedOn($primeView)
                    ->causedBy(auth()->user())
                    ->withProperties([
                        'added_product' => $productNames,
                    ])
                    ->log('Products added to \''.$primeView->name.'\'');
            }

            return $newProducts;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function deletePrimeViewProduct(int $id)
    {
        $prime_view_product = PrimeViewProduct::find($id);

        $prime_view   = $prime_view_product->prime_view;
        $product_name = $prime_view_product->product->name;

        $prime_view_product->delete();

        activity()
            ->useLog('product-delete-from-prime-view')
            ->event('deleted')
            ->performedOn($prime_view)
            ->causedBy(auth()->user())
            ->withProperties([
                'deleted_product' => $product_name,
            ])
            ->log('Product deleted from \''.$prime_view->name.'\'');

        return $prime_view;
    }

    public function updatePrimeViewProduct(int $id, $data)
    {
        try {
            if (! in_array($data['status'], ['active', 'inactive', 'pending', 'rejected'])) {
                throw new \Exception('Invalid status value');
            }

            return PrimeViewProduct::find($id)->update($data);

        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function updateOrder($orderData): bool
    {
        foreach ($orderData as $index => $id) {
            PrimeViewProduct::where('id', $id)->update(['order' => $index + 1]);
        }

        return true;
    }

    public function repositionProduct(int $id, int $newPosition): bool
    {
        $product = PrimeViewProduct::findOrFail($id);
        $oldPosition = (int) $product->order;

        if ($newPosition === $oldPosition) {
            return true;
        }

        $primeViewId = $product->prime_view_id;

        if ($newPosition < $oldPosition) {
            // Moving up: shift records in [newPosition, oldPosition-1] down by 1
            PrimeViewProduct::where('prime_view_id', $primeViewId)
                ->whereBetween('order', [$newPosition, $oldPosition - 1])
                ->increment('order');
        } else {
            // Moving down: shift records in [oldPosition+1, newPosition] up by 1
            PrimeViewProduct::where('prime_view_id', $primeViewId)
                ->whereBetween('order', [$oldPosition + 1, $newPosition])
                ->decrement('order');
        }

        $product->update(['order' => $newPosition]);

        return true;
    }
}
