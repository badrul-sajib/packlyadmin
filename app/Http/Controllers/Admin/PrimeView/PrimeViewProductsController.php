<?php

namespace App\Http\Controllers\Admin\PrimeView;

use Throwable;
use Illuminate\Http\Request;
use App\Services\ProductService;
use App\Services\PrimeViewService;
use App\Models\PrimeView\PrimeView;
use App\Http\Controllers\Controller;
use App\Services\PrimeViewProductService;
use App\Http\Requests\Admin\PrimeViewProductRequest;
use App\Http\Requests\Admin\PrimeViewProductCreateRequest;
use App\Http\Requests\Admin\PrimeViewProductUpdateOrderRequest;

class PrimeViewProductsController extends Controller
{
    protected PrimeViewProductService $primeViewProductService;

    protected PrimeViewService $primeViewService;

    public function __construct(PrimeViewProductService $primeViewProductService, PrimeViewService $primeViewService)
    {
        $this->primeViewProductService = $primeViewProductService;
        $this->primeViewService        = $primeViewService;
        $this->middleware('permission:prime-view-product-list')->only('index');
        $this->middleware('permission:prime-view-product-create')->only(['create', 'store']);
        $this->middleware('permission:prime-view-product-update')->only(['edit', 'update']);
        $this->middleware('permission:prime-view-product-delete')->only('destroy');
    }


    public function create(PrimeViewProductCreateRequest $request)
    {
        $data = $request->validated();

        if(!request()->prime_view_id){
            abort(404);
        }

        $primeView = PrimeView::where('id',request()->prime_view_id)->first();

        if ($request->ajax()) {
            $products = ProductService::getShopLimitProducts($data);

            return response()->json(['products' => $products]);
        }

        return view('Admin::prime-views.add-product', compact('primeView'));
    }

    public function store(PrimeViewProductRequest $request)
    {
        $data = $request->validated();
        $this->primeViewProductService->storePrimeViewProduct($data);

        return response()->json([
            'message' => 'Products added successfully!',
        ]);
    }

    public function update(Request $request, string $id)
    {
        $this->primeViewProductService->updatePrimeViewProduct($id, ['status' => $request->status]);

        return response()->json(['message' => 'Product updated Successfully']);
    }

    public function updateOrder(PrimeViewProductUpdateOrderRequest $request)
    {
        $request->validated();

        $this->primeViewProductService->updateOrder($request->order);

        return response()->json(['success' => 'Order updated successfully']);
    }

    public function reposition(Request $request, int $id)
    {
        $request->validate([
            'position' => 'required|integer|min:1',
        ]);

        $this->primeViewProductService->repositionProduct($id, (int) $request->position);

        return response()->json(['message' => 'Position updated successfully']);
    }

    public function destroy(int $id)
    {
        $this->primeViewProductService->deletePrimeViewProduct($id);

        return response()->json(['message' => 'Product deleted Successfully']);
    }
}
