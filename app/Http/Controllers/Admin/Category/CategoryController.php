<?php

namespace App\Http\Controllers\Admin\Category;

use Illuminate\Http\Request;
use App\Services\CategoryService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryRequest;
use App\Exceptions\CategoryCreationException;
use Psr\Container\NotFoundExceptionInterface;
use Illuminate\Validation\ValidationException;
use Psr\Container\ContainerExceptionInterface;

class CategoryController extends Controller
{
    public function __construct(private readonly CategoryService $categoryService)
    {
        $this->middleware('permission:category-list')->only('index');
        $this->middleware('permission:category-create')->only(['create', 'store']);
        $this->middleware('permission:category-update')->only(['edit', 'update', 'updateCommission', 'getCommissionLogs']);
        $this->middleware('permission:category-delete')->only('destroy');
    }

    public function index(Request $request)
    {
        try {
            $entity = $this->categoryService->getAllCategories($request);

            return customView(['ajax' => 'components.category.table', 'default' => 'Admin::categories.index'], compact('entity'));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Something went wrong');
        }
    }

    public function create(Request $request)
    {
        if ($request->ajax()) {
            if ($request->category_id) {
                $data = $this->categoryService->getSubCategory($request);
            } else {
                $data = $this->categoryService->getMainCategory($request);
            }

            return success('Get categories successfully', $data);
        }

        return view('Admin::categories.create');
    }

    public function store(CategoryRequest $request)
    {
        try {
            $this->categoryService->storeCategory($request->validated());

            return success('Category created successfully');
        } catch (\Throwable $th) {
            return failure('Something went wrong');
        }
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws CategoryCreationException
     */
    public function edit(Request $request, $id)
    {
        if ($request->ajax()) {
            if ($request->category_id) {
                $data = $this->categoryService->getSubCategory($request);
            } else {
                $data = $this->categoryService->getMainCategory($request);
            }

            return success('Get categories successfully', $data);
        }
        $category = $this->categoryService->findCategory($id);

        return view('Admin::categories.edit', compact('category'));
    }

    public function update(CategoryRequest $request, int $id)
    {
        try {
            $this->categoryService->updateCategory($request->validated(), $id);

            return success('Category updated successfully');
        } catch (\Exception $th) {
            return failure('Something went wrong');
        }

    }

    public function updateCommission(Request $request)
    {
        try {
            $commission_type = $request->input('commission_type');
            $request->validate([
                'commission_type' => 'nullable|in:percent,fixed',
                'commission' => 'nullable|numeric|min:0'. ($commission_type == 'percent' ? '|max:100' : ''),
            ]);

            $this->categoryService->updateCommission($request->all());

            return success('Commission updated successfully');
        }catch(ValidationException $ex) {
            return validationError($ex->getMessage(), $ex->errors());
        } catch (\Exception $th) {
            return failure($th->getMessage());
        }
    }

    public function getCommissionLogs(Request $request)
    {
        try {
            $logs = $this->categoryService->getCommissionLogs($request->id);
            return success('Logs fetched successfully', $logs);
        } catch (\Exception $th) {
            return failure($th->getMessage());
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->categoryService->deleteCategory($id);

            return success('Category deleted successfully');
        } catch (\Exception $th) {
            return failure('Something went wrong');
        }
    }
}
