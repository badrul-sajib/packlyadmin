<?php

namespace Modules\Api\V1\Merchant\PrimeView\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Api\V1\Merchant\PrimeView\Http\Requests\PrimeViewRequest;
use App\Services\ApiResponse;
use App\Services\PrimeViewProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PrimeViewProductController extends Controller
{
    private PrimeViewProductService $primeViewService;

    public function __construct(PrimeViewProductService $primeViewService)
    {
        $this->primeViewService = $primeViewService;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $merchantId = auth()->user()->merchant->id;

            $products = $this->primeViewService->getAll($request, $merchantId);

            return ApiResponse::formatPagination('Products retrieved successfully', $products, Response::HTTP_OK);
        } catch (\Exception $e) {
            return ApiResponse::error('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(PrimeViewRequest $request): JsonResponse
    {
        $validator  = $request->validated();
        $merchantId = auth()->user()->merchant->id;

        $productId = $request->product_id;

        $primeView = $this->primeViewService->checkExist($request->prime_view_id, $productId, $merchantId);

        if ($primeView) {
            return ApiResponse::failure('Product already exists', Response::HTTP_CONFLICT);
        }

        $checkShopProduct = $this->primeViewService->checkShopProduct($productId, $merchantId);

        if (! $checkShopProduct) {
            return ApiResponse::failure('Product not found', Response::HTTP_NOT_FOUND);
        }

        try {
            $validatedData = $validator->validated();

            $validatedData['merchant_id'] = auth()->user()->merchant->id;

            $product = $this->primeViewService->create($validatedData);

            return ApiResponse::successMessageForCreate('Product created successfully', $product, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return ApiResponse::error('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $merchantId = auth()->user()->merchant->id;
            $product    = $this->primeViewService->getById($id, $merchantId);
            if ($product) {
                return ApiResponse::success('Product retrieved successfully', $product, Response::HTTP_OK);
            } else {
                return ApiResponse::failure('Product not found', Response::HTTP_NOT_FOUND);
            }
        } catch (\Exception $e) {
            return ApiResponse::error('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(PrimeViewRequest $request, int $id): JsonResponse
    {
        $validator = $request->validated();

        $merchantId = auth()->user()->merchant->id;

        $checkShopProduct = $this->primeViewService->checkExistById($id, $merchantId);

        if (! $checkShopProduct) {
            return ApiResponse::failure('Product not found', Response::HTTP_NOT_FOUND);
        }

        try {
            $merchantId                   = auth()->user()->merchant->id;
            $validatedData                = $validator->validated();
            $validatedData['merchant_id'] = $merchantId;

            $product = $this->primeViewService->update($validatedData, $id, $merchantId);

            if ($product) {
                return ApiResponse::success('Updated successfully', $product, Response::HTTP_OK);
            } else {
                return ApiResponse::failure('Product not found', Response::HTTP_NOT_FOUND);
            }
        } catch (\Exception $e) {
            return ApiResponse::error('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $merchantId = auth()->user()->merchant->id;
            $result     = $this->primeViewService->delete($id, $merchantId);

            if ($result) {
                return ApiResponse::success('Product deleted successfully', Response::HTTP_OK);
            } else {
                return ApiResponse::failure('Product not found', Response::HTTP_NOT_FOUND);
            }
        } catch (\Exception $e) {
            return ApiResponse::error('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
