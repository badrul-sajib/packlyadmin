<?php

namespace App\Http\Controllers\Admin\PricingPlan;

use App\Enums\RecurringTypes;
use App\Http\Controllers\Controller;
use App\Models\PricingPlan\PricingPlan;
use App\Services\ModuleReader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PricingPlanController extends Controller
{
    protected ModuleReader $moduleReader;

    public function __construct(ModuleReader $moduleReader)
    {
        $this->moduleReader = $moduleReader;
        // Add permissions when they are created
        // $this->middleware('permission:pricing-plan-list')->only('index');
        // $this->middleware('permission:pricing-plan-create')->only('create', 'store');
        // $this->middleware('permission:pricing-plan-update')->only('edit', 'update');
        // $this->middleware('permission:pricing-plan-delete')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PricingPlan::query();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $pricingPlans = $query->orderBy('created_at', 'desc')->paginate(15);

        if ($request->ajax()) {
            return view('components.pricing-plan.table', ['entity' => $pricingPlans])->render();
        }

        return view('backend.pages.pricing-plan.index', compact('pricingPlans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $recurringTypes = $this->getRecurringTypes();
        $groupedModules = $this->moduleReader->getGroupedModules();
        $dependencyMap = $this->moduleReader->getDependencyMap();
        $reverseDependencyMap = $this->moduleReader->getReverseDependencyMap();

        return view('backend.pages.pricing-plan.create', compact(
            'recurringTypes',
            'groupedModules',
            'dependencyMap',
            'reverseDependencyMap'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'recurring_type' => 'required|integer|in:1,2,3,4,5,6,7',
            'modules' => 'nullable|array',
            'modules.*' => 'string',
            'status' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $pricingPlan = PricingPlan::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'recurring_type' => $request->recurring_type,
            'modules' => $request->modules ?? [],
            'status' => $request->has('status') ? (bool) $request->status : true,
        ]);

        return response()->json([
            'message' => 'Pricing plan created successfully!',
            'data' => $pricingPlan
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $pricingPlan = PricingPlan::findOrFail($id);
        return response()->json(['data' => $pricingPlan]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $pricingPlan = PricingPlan::findOrFail($id);
        $recurringTypes = $this->getRecurringTypes();
        $groupedModules = $this->moduleReader->getGroupedModules();
        $dependencyMap = $this->moduleReader->getDependencyMap();
        $reverseDependencyMap = $this->moduleReader->getReverseDependencyMap();

        return view('backend.pages.pricing-plan.edit', compact(
            'pricingPlan',
            'recurringTypes',
            'groupedModules',
            'dependencyMap',
            'reverseDependencyMap'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $pricingPlan = PricingPlan::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'recurring_type' => 'required|integer|in:1,2,3,4,5,6,7',
            'modules' => 'nullable|array',
            'modules.*' => 'string',
            'status' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $pricingPlan->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'recurring_type' => $request->recurring_type,
            'modules' => $request->modules ?? [],
            'status' => $request->has('status') ? (bool) $request->status : $pricingPlan->status,
        ]);

        return response()->json([
            'message' => 'Pricing plan updated successfully!',
            'data' => $pricingPlan
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $pricingPlan = PricingPlan::findOrFail($id);
        
        // Add any additional checks here (e.g., if plan is in use by merchants)
        // if ($pricingPlan->merchants()->exists()) {
        //     return response()->json(['message' => 'Cannot delete pricing plan that is in use!'], 422);
        // }

        $pricingPlan->delete();

        return response()->json(['message' => 'Pricing plan deleted successfully!']);
    }

    /**
     * Get all recurring types for dropdown
     */
    protected function getRecurringTypes(): array
    {
        return collect(RecurringTypes::cases())->map(function ($type) {
            return [
                'value' => $type->value,
                'label' => $type->label(),
            ];
        })->toArray();
    }
}
