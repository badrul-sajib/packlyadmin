<?php

namespace App\Http\Controllers\Admin\Category;

use Throwable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Actions\FetchCategoryRequest;
use Spatie\Activitylog\Models\Activity;
use App\Models\Category\CategoryCreateRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Requests\Admin\CategoryCreateRequestRequest;

class CategoryCreateRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:category-request-list')->only('index');
        $this->middleware('permission:category-request-update')->only(['show', 'update']);
        $this->middleware('permission:category-request-delete')->only('destroy');
    }

    /**
     * @throws Throwable
     */
    public function index(Request $request)
    {
        $category_requests = (new FetchCategoryRequest)->execute($request);
        if ($request->ajax()) {
            return view('components.category_requests.table', ['category_requests' => $category_requests])->render();
        }

        return view('backend.pages.category_create_requests.index', compact('category_requests'));
    }

    public function show(int $id)
    {
        try {
            $category_request = CategoryCreateRequest::findOrFail($id);
            $activities       = Activity::where('subject_type', CategoryCreateRequest::class)
                ->where('subject_id', $id)
                ->orderBy('created_at', 'desc')
                ->get();

            return view('backend.pages.category_create_requests.edit', compact('category_request', 'activities'));
        } catch (ModelNotFoundException $e) {
            return redirect()->back()->with('message', 'Category request not found');
        }
    }

    // status update for category request
    public function update(CategoryCreateRequestRequest $request, int $id)
    {
        $request->validated();

        try {
            $categoryRequest  = CategoryCreateRequest::findOrFail($id);
            $old              = $categoryRequest->status;

            $categoryRequest->fill([
                'status' => $request->type,
            ]);

            $status = [
                1 => 'Pending',
                2 => 'Approved',
                3 => 'Rejected',
            ];

            $categoryRequest->save();

            $properties = [
                'old' => $status[$old],
                'new' => $status[$categoryRequest->status],
            ];

            activity()
                ->useLog('category-create-request')
                ->event('updated')
                ->performedOn($categoryRequest)
                ->causedBy(auth()->user())
                ->withProperties($properties)
                ->log('Category request status updated by '.auth()->user()->name);

            return success('Category request status updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return response()->json(['message' => 'An error occurred'], 500);
        }
    }

    public function destroy(int $id)
    {
        try {
            $category_request = CategoryCreateRequest::findOrFail($id);
            $category_request->delete();

            return response()->json(['message' => 'Category request deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return redirect()->back()->with('message', 'Category request not found');
        }
    }
}
