<?php

namespace App\Http\Controllers\Admin\Product;

use App\Enums\ShopProductStatus;
use App\Http\Controllers\Controller;
use App\Models\Draft\Draft;
use App\Models\Product\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductChangeRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:product-changes-list')->only('index', 'show');
        $this->middleware('permission:product-changes-update')->only('productChangesAccept');
    }
    public function index()
    {
        $perPage = request()->input('perPage', 10);
        $search = request()->input('search');
        $merchant_id = request()->input('merchant_id');

        $products = Product::query()
            ->with(['draft','draft.changes','media','merchant:id,name,shop_name,phone'])
            ->whereHas('draft', function ($query) {
                $query->where('status', 'pending');
            })
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");

            })
            ->when($merchant_id, function ($query) use ($merchant_id) {
                $query->where('merchant_id', $merchant_id);
            })
            ->orderBy('updated_at', 'desc')
            ->paginate($perPage);

        if (request()->ajax()) {
            return view('components.product.product_changes_table', ['entity' => $products])->render();
        }

        return view('Admin::product.product_changes', compact('products'));
    }

    public function show($id)
    {
        $product = Product::query()
            ->with([
                'draft',
                'draft.changes',
                'media',
                'merchant:id,name,shop_name,phone',
                'shopProduct:id,product_id,status,active_status',
                'productDetail',
                'variations.variationAttributes.attribute',
                'variations.variationAttributes.attributeOption',
                'variations.media',
            ])
            ->whereHas('draft', function ($query) {
                $query->where('status', 'pending');
            })
            ->findOrFail($id);

        return view('Admin::product.product_change_details', compact('product'));
    }

    public function productChangesAccept($id)
    {
        try {
            $product = Product::where('id', $id)->first();
            $shopProduct = $product->shopProduct;
            $oldStatus = $shopProduct->status;

            DB::beginTransaction();
            $product->setDraftStatus('approved');
            $shopProduct->update(['status' => ShopProductStatus::APPROVED->value]);

            try {
                activity()
                    ->useLog('product-update')
                    ->event('updated')
                    ->performedOn($shopProduct)
                    ->causedBy(auth()->user())
                    ->withProperties([
                        'old' => ShopProductStatus::label()[$oldStatus],
                        'new' => ShopProductStatus::label()[ShopProductStatus::APPROVED->value],
                    ])
                    ->log('Product status changed by '.auth()->user()->name);
            } catch (\Throwable $th) {
                Log::error('Activity log error: '.$th->getMessage());
            }
            DB::commit();

            return redirect()->route('admin.product.changes.index')->with('success', 'Product status changed successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->route('admin.product.changes.index')->with('error', 'Something went wrong');
        }
    }

    public function productChangesReject(Request $request, $id)
    {
        $request->validate([
            'reject_reason' => 'required|string|max:255',
        ]);


        $rejectReason = $request->input('reject_reason');

        try {
            $product = Product::where('id', $id)->first();
            $shopProduct = $product->shopProduct;
            $oldStatus = $shopProduct->status;

            DB::beginTransaction();
            $product->setDraftStatus('rejected');
            $shopProduct->update(['status' => ShopProductStatus::REJECTED->value]);


            try {
                activity()
                    ->useLog('product-update')
                    ->event('updated')
                    ->performedOn($shopProduct)
                    ->causedBy(auth()->user())
                    ->withProperties([
                        'old'   => ShopProductStatus::label()[$oldStatus],
                        'new'   => ShopProductStatus::label()[ShopProductStatus::REJECTED->value],
                        'note'  => $rejectReason,
                    ])
                    ->log('Product status changed by '.auth()->user()->name);
            } catch (\Throwable $th) {
                Log::error('Activity log error: '.$th->getMessage());
            }
            DB::commit();

            return redirect()->route('admin.product.changes.index')->with('success', 'Product status changed successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return redirect()->route('admin.product.changes.index')->with('error', 'Something went wrong');
        }
    }

}
