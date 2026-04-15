<?php

namespace App\Http\Controllers\Admin\PrimeView;

use Throwable;
use Illuminate\Http\Request;
use App\Services\PrimeViewService;
use App\Models\PrimeView\PrimeView;
use App\Http\Controllers\Controller;
use Spatie\Activitylog\Models\Activity;
use App\Services\PrimeViewProductService;
use App\Http\Requests\Admin\PrimeViewRequest;
use App\Http\Requests\Admin\PrimeViewUpdateOrderRequest;

class PrimeViewController extends Controller
{

    public function __construct(
        protected PrimeViewProductService $primeViewProductService,
        protected PrimeViewService $primeViewService
    ){
        $this->primeViewService = $primeViewService;
        $this->middleware('permission:prime-view-list')->only('index');
        $this->middleware('permission:prime-view-create')->only(['create', 'store']);
        $this->middleware('permission:prime-view-update')->only(['edit', 'update']);
        $this->middleware('permission:prime-view-delete')->only('destroy');
    }

    /**
     * @throws Throwable
     */
    public function index(Request $request)
    {
        $primeViews  = $this->primeViewService->getPrimeViews($request);

        $activities  = Activity::where('subject_type', PrimeView::class)->latest()->limit(10)->get();

        if ($request->ajax()) {
            return view('components.prime-views.table', ['entity' => $primeViews])->render();
        }

        return view('Admin::prime-views.index', compact('primeViews', 'activities'));
    }

    public function store(PrimeViewRequest $request)
    {
        $data = $request->validated();

        // if request not has show_on_sticky
        if (! isset($data['show_on_sticky'])) {
            $data['show_on_sticky'] = 0;
        }

        // if request not has show_on_sticky
        if (! isset($data['explore_item'])) {
            $data['explore_item'] = 0;
        }

        $this->primeViewService->storePrimeView($data);

        return response()->json(['success' => 'Prime View Created Successfully']);
    }

    /**
     * @throws Throwable
     */
    public function edit(PrimeView $primeView)
    {
        return view('components.prime-views.form', ['data' => $primeView])->render();
    }

    public function show(Request $request, PrimeView $primeView)
    {
        $request->merge(['prime_view_id'=> $primeView->id]);
        $products =  $this->primeViewProductService->getProducts($request);

        if ($request->ajax()) {
            return view('components.prime-views.product_table', ['entity' => $products])->render();
        }

        return view('Admin::prime-views.show', ['primeView' => $primeView,'products'=> $products])->render();
    }

    public function update(PrimeViewRequest $request, $id)
    {
        $data = $request->validated();

        // if request not has show_on_sticky
        if (! isset($data['show_on_sticky'])) {
            $data['show_on_sticky'] = 0;
        }

        // if request not has show_on_sticky
        if (! isset($data['explore_item'])) {
            $data['explore_item'] = 0;
        }

        $this->primeViewService->updatePrimeView($id, $data);

        return response()->json(['message' => 'Prime View updated Successfully']);
    }

    public function updateOrder(PrimeViewUpdateOrderRequest $request)
    {
        $request->validated();

        $this->primeViewService->updateOrder($request->order);

        return response()->json(['success' => 'Order updated successfully']);
    }

    public function destroy(int $id)
    {
        try {
            $this->primeViewService->deletePrimeView($id);

            return response()->json(['success' => 'Prime View deleted Successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Prime View cannot be deleted'. $e->getMessage()], 422);
        }
    }
}
