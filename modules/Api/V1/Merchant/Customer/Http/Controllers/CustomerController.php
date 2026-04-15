<?php

namespace Modules\Api\V1\Merchant\Customer\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\Merchant\CustomerResource;
use App\Models\Customer\Customer;
use App\Models\Sell\SellProduct;
use App\Services\ApiResponse;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Modules\Api\V1\Merchant\Customer\Http\Requests\CustomerRequest;
use Symfony\Component\HttpFoundation\Response;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('shop.permission:show-customers')->only('index', 'show');
        $this->middleware('shop.permission:create-customer')->only('store');
        $this->middleware('shop.permission:update-customer')->only('update');
        $this->middleware('shop.permission:delete-customer')->only('destroy');
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $merchantId = Auth::user()->merchant?->id;
            $customerQuery = Customer::query()->where('merchant_id', auth()->user()->merchant?->id);
            $customerQuery = Customer::query()
                ->where('customers.merchant_id', $merchantId)
                ->leftJoinSub(
                    SellProduct::query()
                        ->selectRaw('customer_id,
                        COUNT(*) as sell_product_count,
                        COALESCE(SUM(grand_total),0) as total_sales_amount,
                        COALESCE(SUM(paid_amount),0) as total_paid_amount,
                        COALESCE(SUM(due_amount),0) as total_due_amount
                    ')
                        ->where('merchant_id', $merchantId)
                        ->groupBy('customer_id'),
                    'sell_summary',
                    'sell_summary.customer_id',
                    '=',
                    'customers.id'
                )
                ->select(
                    'customers.*',
                    'sell_summary.sell_product_count',
                    'sell_summary.total_sales_amount',
                    'sell_summary.total_paid_amount',
                    'sell_summary.total_due_amount'
                );

            if ($request->has('search')) {
                $customerQuery->where(function ($query) use ($request) {
                    $query->where('name', 'LIKE', "%$request->search%")
                        ->orWhere('phone', 'LIKE', "%$request->search%");
                });
            }

            $perPage = $request->query('per_page', 10);
            $customers = $customerQuery->orderBy('id', 'desc')->paginate($perPage);
            $customersResource = CustomerResource::collection($customers);

            return ApiResponse::formatPagination('customers retrieved successfully', $customersResource, Response::HTTP_OK);
        } catch (Exception $e) {
            return ApiResponse::failure('customer not found.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(CustomerRequest $request): JsonResponse
    {
        try {
            $request->validated();

            $customer = Customer::create([
                'merchant_id' => auth()->user()->merchant?->id,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'customer_type_id' => $request->customer_type_id,
            ]);

            $customer->image = $request->image;
            $customer->save();

            return ApiResponse::successMessageForCreate('customer created successfully.', $customer, Response::HTTP_CREATED);
        } catch (ValidationException $v) {
            $errors = $v->validator->errors();

            return ApiResponse::validationError('There were validation errors. ', $errors, Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Exception $e) {
            return ApiResponse::failure('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(Request $request, int $id): JsonResponse
    {
        try {
            $customer = Customer::where('merchant_id', Auth::user()->merchant->id)
                ->where('id', $id)
                ->select('id', 'name', 'phone', 'email', 'address', 'customer_type_id')
                ->firstOrFail();

            $sell_product_summary = SellProduct::where('customer_id', $id)
                ->where('merchant_id', Auth::user()->merchant->id)
                ->selectRaw('COUNT(*) as sell_product_count, SUM(grand_total) as total_sales_amount, SUM(paid_amount) as total_paid_amount, SUM(due_amount) as total_due_amount')
                ->first();

            $perPage = $request->query('per_page', 10);
            $orders = SellProduct::where('customer_id', $id)
                ->where('merchant_id', Auth::user()->merchant->id)
                ->orderBy('id', 'desc')
                ->paginate($perPage);
            $formattedOrders = ApiResponse::formatPagination('Orders retrieved successfully.', $orders, Response::HTTP_OK);

            $data = [
                'customer' => $customer,
                'sell_product_summary' => $sell_product_summary,
                'orders' => $formattedOrders->getData(true), // Retrieve formatted orders as an array
            ];

            return ApiResponse::success('Customer details retrieved successfully.', $data, Response::HTTP_OK);
        } catch (ModelNotFoundException) {
            return ApiResponse::failure('Customer not found', Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return ApiResponse::failure('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(CustomerRequest $request, int $id): JsonResponse
    {
        try {
            $customer = Customer::where('merchant_id', Auth::user()->merchant->id)
                ->where('id', $id)
                ->firstOrFail();

            $request->validated();

            $customer->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'customer_type_id' => $request->customer_type_id,
            ]);

            $customer->image = $request->image;
            $customer->save();

            return ApiResponse::success('customer updated successfully.', $customer, Response::HTTP_OK);
        } catch (ModelNotFoundException) {
            return ApiResponse::failure('Customer not found ', Response::HTTP_NOT_FOUND);
        } catch (ValidationException $v) {
            return ApiResponse::validationError('There were validation errors. ', $v->validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Exception $e) {
            return ApiResponse::failure('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(int $id)
    {
        try {
            $customer = Customer::where('merchant_id', Auth::user()->merchant->id)
                ->where('id', $id)
                ->firstOrFail();

            if ($customer) {
                $sellProduct = SellProduct::where('customer_id', $customer->id)
                    ->where('merchant_id', Auth::user()->merchant->id)
                    ->first();

                if ($sellProduct) {
                    return ApiResponse::failure('Customer data exists with sales', Response::HTTP_CONFLICT);
                }

                $customer->delete();

                return ApiResponse::success('Customer deleted successfully.', Response::HTTP_OK);
            }
        } catch (ModelNotFoundException) {
            return ApiResponse::failure('Customer not found', Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return ApiResponse::failure('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
