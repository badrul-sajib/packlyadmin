<?php

namespace App\Http\Controllers\Admin\Commission;

use App\Actions\FetchCommission;
use App\Exceptions\CommissionValidateException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CommissionRequest;
use App\Models\Category\Category;
use App\Models\Merchant\Commission;
use App\Services\CommissionService;
use Illuminate\Http\Request;
use Throwable;

class CommissionController extends Controller
{
    protected CommissionService $commissionService;

    public function __construct(CommissionService $commissionService)
    {
        $this->commissionService = $commissionService;
        $this->middleware('permission:commission-list')->only('index');
        $this->middleware('permission:commission-create')->only(['create', 'store']);
        $this->middleware('permission:commission-update')->only(['edit', 'update']);
        $this->middleware('permission:commission-delete')->only('destroy');
    }

    /**
     * Display a listing of the commissions.
     *
     * @throws Throwable
     */
    public function index(Request $request)
    {
        $commissions = (new FetchCommission)->execute($request);
        if ($request->ajax()) {
            return view('components.commission.table', ['entity' => $commissions])->render();
        }

        return view('Admin::commissions.index', compact('commissions'));
    }

    /**
     * Show the form for creating a new commission.
     */
    public function create()
    {
        $categories = Category::all();

        return view('Admin::commissions.create', get_defined_vars());
    }

    /**
     * Store a newly created commission in storage.
     */
    public function store(CommissionRequest $request)
    {
        try {
            $this->commissionService->store($request->validated());

            return response()->json(['success' => 'Commission created successfully!']);
        } catch (CommissionValidateException $c) {
            
            return response()->json(['message' => 'Commission Validate Exception'], 402);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Commission Validate Exception'], 500);
        }
    }

    /**
     * Show the form for editing the specified commission.
     */
    public function edit(Commission $commission)
    {
        return view('Admin::commissions.edit', compact('commission'));
    }

    /**
     * Update the specified commission in storage.
     */
    public function update(CommissionRequest $request, Commission $commission)
    {
        $this->commissionService->update($commission, $request->validated());

        return response()->json(['success' => 'Commission updated successfully!']);
    }

    /**
     * Remove the specified commission from storage.
     */
    public function destroy(Commission $commission)
    {
        $this->commissionService->delete($commission);

        return response()->json(['success' => 'Commission deleted successfully!']);
    }
}
