<?php

namespace App\Http\Controllers\Admin\Page;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\EPageRequest;
use App\Services\EPageService;
use Illuminate\Http\Request;
use Throwable;

class EPageController extends Controller
{
    public function __construct(private readonly EPageService $ePageService)
    {
        $this->middleware('permission:e-page-list', ['only' => ['index']]);
        $this->middleware('permission:e-page-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:e-page-update', ['only' => ['edit', 'update']]);
        $this->middleware('permission:e-page-delete', ['only' => ['destroy']]);
    }

    /**
     * @throws Throwable
     */
    public function index(Request $request)
    {
        $e_pages = $this->ePageService->getEPages($request);

        if ($request->ajax()) {
            return view('components.e_pages.table', ['entity' => $e_pages])->render();
        }

        return view('Admin::e_pages.index', compact('e_pages'));
    }

    public function create()
    {
        return view('Admin::e_pages.create');
    }

    public function edit(int $id)
    {
        $e_page = $this->ePageService->getById($id);

        return view('Admin::e_pages.edit', compact('e_page'));
    }

    public function store(EPageRequest $request)
    {
        try {
            $this->ePageService->store($request->validated());

            return response()->json(['message' => 'EPage created successfully!']);
        } catch (Throwable $th) {
            return response()->json(['error' =>'Something went wrong']);
        }
    }

    public function update(EPageRequest $request, int $id)
    {
        try {
            $this->ePageService->update($request->validated(), $id);

            return response()->json(['message' => 'EPage created successfully!']);
        } catch (Throwable $th) {
            return response()->json(['error' => 'Something went wrong']);
        }
    }

    public function destroy(int $id)
    {
        $this->ePageService->delete($id);

        return response()->json(['message' => 'EPage deleted successfully!']);
    }
}
