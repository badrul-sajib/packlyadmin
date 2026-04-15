<?php

namespace App\Http\Controllers\Admin\Product;

use Throwable;
use App\Models\Unit\Unit;
use App\Models\Brand\Brand;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\OrderService;
use App\Models\Product\Product;
use App\Enums\ShopProductStatus;
use App\Services\ProductService;
use App\Models\Category\Category;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Category\SubCategory;
use App\Actions\BarcodeImageGenerator;
use App\Models\Category\SubCategoryChild;
use App\Exceptions\ShopProductStatusException;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Admin\BulkProductStatusRequest;
use App\Http\Requests\Admin\RequestProductStatusRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Requests\Admin\Product\ProductRequest as AdminProductRequest;

class ProductController extends Controller
{
    public function __construct(
        private ProductService $productService
    ) {
        $this->middleware('permission:product-request-list')->only('requestProducts');
        $this->middleware('permission:product-request-update')->only('requestProductStatus');

        $this->middleware('permission:shop-product-list')->only('shopProducts');
        // "shop-product-show" and "product-request-show" permissions are checked in show method
    }

    /**
     * @throws Throwable
     */
    public function requestProducts(Request $request)
    {
        $products     = ProductService::requestProducts($request);

        if ($request->ajax()) {
            return view('components.product.request_table', compact('products'))->render();
        }

        $shopStatuses = ShopProductStatus::label();

        return view('Admin::product.product_request', compact('products', 'shopStatuses'));
    }

    public function requestProductStatus(RequestProductStatusRequest $request)
    {
        try {

            $data = $request->validated();
            ProductService::requestProductStatus((object) $data);
            return response()->json(['message' => 'Product status updated successfully']);
        } catch (ShopProductStatusException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function bulkProductStatus(BulkProductStatusRequest $request)
    {
        try {

            $data = $request->validated();
            $this->productService->bulkProductStatus($data);
            return response()->json(['message' => 'Product status updated successfully']);
        } catch (\App\Exceptions\ShopProductStatusException $e) {
            return response()->json(['message' => $e->getMessage()], \Symfony\Component\HttpFoundation\Response::HTTP_BAD_REQUEST);
        } catch (\Throwable $th) {

            Log::error($th->getMessage());
            return response()->json(['message' => 'Something went wrong']);
        }
    }

    /**
     * @throws Throwable
     */
    public function shopProducts(Request $request)
    {
        $products = ProductService::getShopProducts($request);
        if ($request->ajax()) {
            return view('components.product.table', compact('products'))->render();
        }

        $shopStatuses = ShopProductStatus::label();

        return view('Admin::product.shop_products', compact('products', 'shopStatuses'));
    }
    public function products(Request $request)
    {
        $products = ProductService::getAllProducts($request);
        if ($request->ajax()) {
            return view('components.product.table', compact('products'))->render();
        }

        $shopStatuses = ShopProductStatus::label();

        return view('Admin::product.products', compact('products', 'shopStatuses'));
    }

    private function updateBarcode(Product $product): void
    {
        if ($product->barcode === null || $product->barcode === '') {
            $product->barcode = Product::generateUnique12DigitBarcode();
            $product->save();
        }
        $product->barcode;
    }

    public function show($slug)
    {
        try {
            $user = auth()->user();

            $product = ProductService::getProductBySlug($slug);

            $product->barcode ?: $this->updateBarcode($product);

            $barcodeImage = BarcodeImageGenerator::execute($product);
            $activities   = $product->shopProduct?->activities()?->with('causer')->orderBy('created_at', 'desc')->get();
            $isPending   = $product->shopProduct?->status == ShopProductStatus::PENDING->value;

            return view('Admin::product.show', compact('product', 'barcodeImage', 'activities', 'isPending'));
        } catch (ModelNotFoundException) {
            return redirect()->back()->with('error', 'Product not found');
        }
    }

    public function edit($slug)
    {
        try {

            $product = ProductService::getProductBySlug($slug);

            if ($product->shopProduct?->status != ShopProductStatus::PENDING->value) {
                return redirect()->route('admin.product.show', $product->slug)->with('error', 'Only products with pending status can be edited.');
            }

            $activities      = $product->activities()?->with('causer')->orderBy('created_at', 'desc')->get();
            $categories      = Category::where('status', 1)->orderBy('name')->get();
            $subCategories   = SubCategory::where('category_id', $product->category_id)->where('status', 1)->orderBy('name')->get();
            $childCategories = SubCategoryChild::where('sub_category_id', $product->sub_category_id)->where('status', 1)->orderBy('name')->get();
            $brands          = Brand::where('status', 1)->orderBy('name')->get();
            $units           = Unit::where('merchant_id', $product->merchant_id)->where('status', 1)->orderBy('name')->get();
            $warrantyType    = $this->decodeWarranty($product->warranty_type);
            $product->warranty_type = $warrantyType;

            // echo '<pre>';
            // echo print_r($product->toArray());
            // echo '</pre>';

            return view('Admin::product.edit', compact('product', 'activities', 'categories', 'subCategories', 'childCategories', 'brands', 'units', 'warrantyType'));
        } catch (ModelNotFoundException) {
            return redirect()->back()->with('error', 'Product not found');
        }
    }

    public function categoryGet(Request $request)
    {


        $type         = $request->type;
        $relatedId    = $request->related_id;

        switch ($type) {
            case 'sub_category':
                $category = SubCategory::where('category_id', $relatedId)
                    ->where('status', 1)
                    ->orderBy('name')
                    ->get(['id', 'name']);
                $category->prepend(['id' => '', 'name' => 'Select Sub Category']);
                break;

            case 'sub_category_child':
                $category = SubCategoryChild::where('sub_category_id', $relatedId)
                    ->where('status', 1)
                    ->orderBy('name')
                    ->get(['id', 'name']);
                $category->prepend(['id' => '', 'name' => 'Select Child Category']);
                break;
            default:
                $category = [];
                break;
        }
        return response()->json($category);
    }

    private function decodeWarranty($value): array
    {
        if (is_array($value)) {
            return $value;
        }

        $decoded = json_decode($value ?? '', true);
        // handle double-encoded strings
        if (is_string($decoded)) {
            $decoded = json_decode($decoded, true);
        }

        return $decoded ?: [];
    }

    public function update(AdminProductRequest $request, $slug)
    {
        try {
            $product = ProductService::getProductBySlug($slug);

            if ($product->shopProduct?->status != ShopProductStatus::PENDING->value) {
                return redirect()->route('admin.product.show', $product->slug)->with('error', 'Only products with pending status can be edited.');
            }

            $validated = $request->validated();

            $warrantyType = [
                'replace' => [
                    'status'           => $request->has('replace_status') ? 1 : 0,
                    'recurring_period' => (string) $request->input('replace_recurring_period'),
                    'recurring_type'   => (string) $request->input('replace_recurring_type'),
                ],
                'service' => [
                    'status'           => $request->has('service_status') ? 1 : 0,
                    'recurring_period' => (string) $request->input('service_recurring_period'),
                    'recurring_type'   => (string) $request->input('service_recurring_type'),
                ],
            ];

            $data = [
                'name'                  => $validated['name'],
                'slug'                  => $this->generateUniqueSlug($validated['name'], $product->id),
                'sku'                   => $validated['sku'] ?? $product->sku,
                'category_id'           => $validated['category_id'],
                'sub_category_id'       => $validated['sub_category_id'] ?? null,
                'sub_category_child_id' => $validated['sub_category_child_id'] ?? null,
                'brand_id'              => $validated['brand_id'] ?? null,
                'unit_id'               => $validated['unit_id'] ?? null,
                'weight'                => $validated['weight'] ?? null,
                'has_warranty'          => (int) $validated['has_warranty'],
                'warranty_note'         => $validated['warranty_note'] ?? null,
                'warranty_type'         => json_encode($warrantyType),
                'description'           => $validated['description'] ?? '',
                'specification'         => $validated['specification'] ?? '',
            ];

            ProductService::updateProduct($data, $product);

            // Handle thumbnail removal
            if (($validated['remove_thumbnail'] ?? '0') === '1') {
                $product->deleteMediaCollection('thumbnail');
            }

            // Handle new thumbnail upload
            if ($request->hasFile('thumbnail')) {
                $product->thumbnail = $request->file('thumbnail');
                $product->save();
            }

            // Handle gallery image removal
            if (! empty($validated['remove_images'])) {
                $ids = json_decode($validated['remove_images'], true) ?? [];

                if (is_array($ids) && count($ids) > 0) {
                    foreach ($ids as $id) {
                        $product->deleteMedia($id);
                    }
                }
            }

            // Handle new gallery images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $product->addMedia($image, 'images');
                }
            }

            return response()->json(['message' => 'Product updated successfully', 'redirect' => route('admin.product.show', $product->slug)]);
        } catch (ModelNotFoundException) {
            return redirect()->back()->with('error', 'Product not found');
        }
    }

    public function MerchantCategoryProducts(Request $request)
    {
        try {
            return ProductService::getMerchantCategoryProducts($request);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }

    public function productVariant(int $id)
    {
        $product = Product::find($id);
        $variant = $product->variations()->get()->map(function ($item) {
            return [
                'id'      => $item->id,
                'sku'     => $item->sku,
                'variant' => OrderService::getOrderItemVariantText($item->variations ?? []),
            ];
        });

        return response()->json([
            'id'      => $product->id,
            'name'    => $product->name,
            'variant' => $variant,
        ]);
    }

    public function ajaxProducts(Request $request)
    {
        $search   = $request->search ?? '';
        $products = ProductService::getShopProductsForBadge($search);

        return response()->json(['products' => $products]);
    }

    /**
     * @throws Throwable
     */
    public function ajaxProductVariation(Request $request)
    {
        $ids = $request->ids ?? [];

        return view('Admin::badges.variation', compact('ids'))->render();
    }

    protected function generateUniqueSlug($name, $id): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;

        while (Product::where('slug', $slug)->where('id', '!=', $id)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
