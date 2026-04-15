<?php

namespace Modules\Api\V1\Merchant\Product\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product\Product;
use App\Services\ApiResponse;
use App\Services\Common\ProductImportService;
use App\Services\Merchant\Product\ProductDeleteService;
use App\Services\Merchant\Product\ProductListService;
use App\Services\Merchant\Product\ProductRestoreService;
use App\Services\Merchant\Product\ProductSearchService;
use App\Services\Merchant\Product\ProductShowService;
use App\Services\Merchant\Product\ProductStoreService;
use App\Services\Merchant\Product\ProductUpdateService;
use App\Services\Merchant\Product\ProductVariationService;
use App\Services\Merchant\Product\ProductVariationUpdateService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Modules\Api\V1\Merchant\Product\Http\Requests\BulkProductCsvRequest;
use Modules\Api\V1\Merchant\Product\Http\Requests\ProductCsvRowRequest;
use Modules\Api\V1\Merchant\Product\Http\Requests\ProductRequest;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ProductController extends Controller
{
    public function __construct(
        protected ProductImportService $productImportService,
        protected ProductUpdateService $productUpdateService,
        protected ProductStoreService $productStoreService,
        protected ProductListService $productListService,
        protected ProductShowService $productShowService,
        protected ProductDeleteService $productDeleteService,
        protected ProductRestoreService $productRestoreService,
        protected ProductSearchService $productSearchService,
        protected ProductVariationService $productVariationService,
        protected ProductVariationUpdateService $productVariationUpdateService,
    ) {
        $this->middleware('shop.permission:show-products')->only('index', 'show', 'search', 'variations');
        $this->middleware('shop.permission:create-product')->only('store', 'import');
        $this->middleware('shop.permission:update-product')->only('update', 'updateVariations', 'productStatusChange');
        $this->middleware('shop.permission:delete-product')->only('destroy');
        $this->middleware('shop.permission:manage-product-trash')->only('trashList', 'restore', 'hideFromTrash');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $merchantId = Auth::user()->merchant->id;

        return $this->productListService->fetchProducts($request, $merchantId);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @throws Exception|Throwable
     */
    public function store(ProductRequest $request): JsonResponse
    {
        try {
            $merchantId = Auth::user()->merchant->id;

            return $this->productStoreService->storeProduct($request, $merchantId);
        } catch (ValidationException $e) {
            return ApiResponse::failure('Something went wrong', Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Exception $e) {
            return ApiResponse::failure('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug): JsonResponse
    {
        $merchantId = Auth::user()->merchant->id;

        return $this->productShowService->showProduct($slug, $merchantId);
    }

    /**
     * Update the specified resource in storage.
     *
     * @throws Throwable
     */
    public function update(ProductRequest $request, $slug): JsonResponse
    {
        try {
            $merchantId = Auth::user()->merchant->id;

            return $this->productUpdateService->updateProduct($request, $slug, $merchantId);
        } catch (ValidationException $e) {
            return ApiResponse::failure('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $product = Product::where('merchant_id', auth()->user()->merchant?->id)->findOrFail($id);

            return $this->productDeleteService->deleteProduct($product);
        } catch (ModelNotFoundException) {
            return ApiResponse::failure('Product not found.', Response::HTTP_NOT_FOUND);
        }
    }

    public function trashList(): JsonResponse
    {
        $merchantId = Auth::user()->merchant?->id;

        return $this->productRestoreService->fetchTrashedProducts($merchantId);
    }

    public function restore(int $id): JsonResponse
    {
        try {
            $product = Product::onlyTrashed()
                ->where('merchant_id', auth()->user()->merchant?->id)
                ->where('is_hidden', false)
                ->findOrFail($id);

            return $this->productRestoreService->restoreProduct($product);
        } catch (ModelNotFoundException) {
            return ApiResponse::failure('Product not found.', Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return ApiResponse::failure('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function hideFromTrash(int $id): JsonResponse
    {
        try {
            $product = Product::onlyTrashed()
                ->where('merchant_id', auth()->user()->merchant?->id)
                ->where('is_hidden', false)
                ->findOrFail($id);

            return $this->productRestoreService->hideFromTrash($product);
        } catch (ModelNotFoundException) {
            return ApiResponse::failure('Product not found.', Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return ApiResponse::failure('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function search(Request $request): JsonResponse
    {
        $merchantId = Auth::user()->merchant->id;

        return $this->productSearchService->searchProducts($request, $merchantId);
    }

    public function variations(string $slug): JsonResponse
    {
        $merchantId = Auth::user()->merchant->id;

        return $this->productVariationService->fetchVariations($slug, $merchantId);
    }

    /**
     * Update product variations
     */
    public function updateVariations(Request $request, string $slug): JsonResponse
    {
        $merchantId = Auth::user()->merchant->id;

        return $this->productVariationUpdateService->updateVariations($request, $slug, $merchantId);
    }

    public function productStatusChange($slug): JsonResponse
    {
        $product = Product::where('slug', $slug)->first();

        $product->status = $product->status == 1 ? '0' : '1';
        $product->save();

        return ApiResponse::successMessageForCreate('Product status updated successfully.', Response::HTTP_OK);
    }

    public function import(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return ApiResponse::failure('Invalid JSON data.', Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $rowRules = (new ProductCsvRowRequest)->rules();
            $bulkRules = (new BulkProductCsvRequest)->rules();
            $merchantId = Auth::user()->merchant->id;
            $paymentDate = $request->payment_date ?? null;

            return $this->productImportService->processImport($data, $rowRules, $bulkRules, $merchantId, $paymentDate);
        } catch (Exception $e) {
            return ApiResponse::failure('Import failed ', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function validateImport(Request $request)
    {
        $data = $request->json()->all();

        $rowRules  = (new ProductCsvRowRequest())->rules();    // first row structure
        $bulkRules = (new BulkProductCsvRequest())->rules();   // products.* + bulk rules

        $merchantId = auth()->user()->merchant?->id; // or however you get it

        return $this->productImportService->validateOnlyImport($data, $rowRules, $bulkRules, $merchantId);
    }
}
