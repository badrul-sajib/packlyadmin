<?php

namespace App\Http\Controllers\Admin\Brand;

use App\Actions\FetchBrand;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BrandRequest;
use App\Models\Brand\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class BrandController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:brand-list')->only('index', 'merchantBrands');
        $this->middleware('permission:brand-create')->only('create', 'store');
        $this->middleware('permission:brand-update')->only('edit', 'update');
        $this->middleware('permission:brand-delete')->only('destroy');
    }

    public function merchantBrands(Request $request)
    {
        $search        = $request->search        ?? '';
        $merchantIds   = $request->merchant_ids  ?? [];
        $merchantType  = $request->merchant_type ?? '';

        $query = DB::table('brands');

        if ($search) {
            $query->where('name', 'like', '%'.$search.'%');
        }
        if ($merchantType == '1' && $merchantIds) {
            $query->whereNotIn('merchant_id', $merchantIds);
        }
        if ($merchantType == '2' && $merchantIds) {
            $query->whereIn('merchant_id', $merchantIds);
        }

        return success('Brands fetched successfully', $query->get());
    }

    /**
     * @throws Throwable
     */
    public function index(Request $request)
    {
        $brands = (new FetchBrand)->execute($request);

        if ($request->ajax()) {
            return view('components.brand.table', ['entity' => $brands])->render();
        }

        return view('Admin::brands.index', compact('brands'));
    }

    public function create()
    {
        return view('Admin::brands.create');
    }

    public function store(BrandRequest $request)
    {
        $request->validated();

        $brand = Brand::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        if ($request->hasFile('image')) {
            $brand->image = $request->file('image');
            $brand->save();
        }

        return response()->json(['message' => 'Brand created successfully!', 'brand' => $brand]);

    }

    public function edit(Brand $brand)
    {
        return view('Admin::brands.edit', compact('brand'));
    }

    public function update(BrandRequest $request, Brand $brand)
    {
        $request->validated();

        $brand->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        if ($request->hasFile('image')) {
            $brand->image = $request->file('image');
            $brand->save();
        }

        return response()->json(['message' => 'Brand updated successfully!']);

    }

    public function destroy(Brand $brand)
    {
        if ($brand->products()->exists()) {
            return response()->json(['message' => 'Brand has products, cannot delete!'], 422);
        }
        $brand->delete();

        return response()->json(['message' => 'Brand deleted successfully!']);
    }
}
