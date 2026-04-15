<?php

namespace App\Http\Controllers\Admin\Reason;

use App\Actions\FetchReason;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReasonRequest;
use App\Services\ReasonService;
use Illuminate\Http\Request;
use Throwable;

class ReasonController extends Controller
{
    protected ReasonService $reasonService;

    public function __construct(ReasonService $reasonService)
    {
        $this->reasonService = $reasonService;
        $this->middleware('permission:reason-list')->only('index');
        $this->middleware('permission:reason-create')->only(['create', 'store']);
        $this->middleware('permission:reason-update')->only(['edit', 'update']);
        $this->middleware('permission:reason-delete')->only('destroy');
    }

    /**
     * @throws Throwable
     */
    public function index(Request $request)
    {
        $reasons = (new FetchReason)->execute($request);
        if ($request->ajax()) {
            return view('components.reason.table', ['entity' => $reasons])->render();
        }

        return view('Admin::reasons.index', compact('reasons'));
    }

    public function create()
    {
        return view('Admin::reasons.create');
    }

    public function store(ReasonRequest $request)
    {
        $data = $request->validated();
        $this->reasonService->createReason($data);

        return response()->json(['message' => 'Reason created successfully!']);
    }

    public function edit(int $id)
    {
        $reason = $this->reasonService->getReason($id);

        return view('Admin::reasons.edit', compact('reason'));
    }

    public function update(ReasonRequest $request, int $id)
    {
        $data = $request->validated();
        $this->reasonService->updateReason($id, $data);

        return response()->json(['message' => 'Reason updated successfully!']);
    }

    public function destroy(int $id)
    {
        $this->reasonService->deleteReason($id);

        return response()->json(['message' => 'Reason deleted successfully!']);
    }
}
