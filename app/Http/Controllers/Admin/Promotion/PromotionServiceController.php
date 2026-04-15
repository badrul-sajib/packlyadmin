<?php

namespace App\Http\Controllers\Admin\Promotion;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PromotionServiceRequest;
use App\Services\PromotionServiceService;
use Illuminate\Http\Request;
use Throwable;

class PromotionServiceController extends Controller
{
    public function __construct(private readonly PromotionServiceService $promotionServiceService)
    {
        $this->middleware('permission:promotion-service-list', ['only' => ['index']]);
        $this->middleware('permission:promotion-service-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:promotion-service-update', ['only' => ['edit', 'update']]);
        $this->middleware('permission:promotion-service-delete', ['only' => ['destroy']]);
    }

    /**
     * @throws Throwable
     */
    public function index(Request $request)
    {
        $promotion_service = $this->promotionServiceService->getPromotionServices($request);

        if ($request->ajax()) {
            return view('components.promotion_service.table', ['entity' => $promotion_service])->render();
        }

        return view('Admin::promotion_service.index', compact('promotion_service'));
    }

    public function create()
    {
        return view('Admin::promotion_service.create');
    }

    public function edit(int $id)
    {
        $e_page = $this->promotionServiceService->getById($id);

        return view('Admin::promotion_service.edit', compact('e_page'));
    }

    public function store(PromotionServiceRequest $request)
    {
        try {
            $this->promotionServiceService->store($request->validated());

            return response()->json(['message' => 'PromotionService created successfully!']);
        } catch (Throwable $th) {
            return response()->json(['error' => 'Something went wrong']);
        }
    }

    public function update(PromotionServiceRequest $request, int $id)
    {
        try {
            $this->promotionServiceService->update($request->validated(), $id);

            return response()->json(['message' => 'PromotionService updated successfully!']);
        } catch (Throwable $th) {
            return response()->json(['error' => 'Something went wrong']);
        }
    }

    public function destroy(int $id)
    {
        $this->promotionServiceService->delete($id);

        return response()->json(['message' => 'PromotionService deleted successfully!']);
    }
}
