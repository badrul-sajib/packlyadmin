<?php

namespace App\Http\Controllers\Admin\Category;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryImportRequest;
use App\Imports\CategoriesImport;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class CategoryImportController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:category-IMPORT')->only('import');
    }

    public function import(CategoryImportRequest $request)
    {
        try {
            $request->validated();

            Excel::import(new CategoriesImport, $request->file('file'));

            return response()->json(['message' => 'Categories imported successfully'], 200);
        } catch (ValidationException $v) {
            return response()->json(['error' => $v->validator->errors()->first()], 422);
        } catch (\Maatwebsite\Excel\Exceptions\NoTypeDetectedException $e) {
            return response()->json(['error' => 'File type not supported'], 400);
        } catch (\Exception $e) {
            Log::error($e);

            return response()->json(['error' => 'Something went wrong, please try again later'], 500);
        }
    }
}
