<?php

namespace Modules\Api\V1\Merchant\Return\Http\Controllers;

use App\Enums\ItemStatus;
use App\Http\Controllers\Controller;
use Modules\Api\V1\Merchant\Return\Http\Requests\ReturnRequest;
use App\Models\Merchant\MerchantOrder;
use App\Models\Order\OrderItemCase;
use App\Models\Shop\ShopSetting;
use App\Services\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ReturnController extends Controller
{
    public function __construct()
    {
        $this->middleware('shop.permission:show-returns')->only('index', 'show', 'steadfastReturnStatus');
        $this->middleware('shop.permission:update-return')->only('update');
    }

    public function index(Request $request): JsonResponse
    {
        $merchantId = $request->user()->merchant->id;
        $perPage    = $request->input('per_page', 10);

        $returnCases = OrderItemCase::with(['orderItem.product', 'orderItem.merchantOrder', 'reason', 'orderItem.product_variant'])
            ->where('type', 'return')
            ->whereHas('orderItem', function ($query) use ($merchantId) {
                $query->whereHas('merchantOrder', function ($subQuery) use ($merchantId) {
                    $subQuery->where('merchant_id', $merchantId);
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->input('status'));
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        $restructuredData = $returnCases->getCollection()->map(function ($item) {
            $baseData = [
                'id'               => $item->id,
                'note'             => $item->note,
                'merchant_note'    => $item->merchant_note,
                'customer_name'    => $item->orderItem->merchantOrder->order->customer_name,
                'customer_address' => $item->orderItem->merchantOrder->order->customer_address,
                'customer_phone'   => $item->orderItem->merchantOrder->order->customer_number,
                'type'             => $item->type,
                'status'           => $item->status,
                'created_at'       => $item->created_at,
                'updated_at'       => $item->updated_at,
                'reason'           => [
                    'id'   => $item->reason->id,
                    'name' => $item->reason->name,
                ],
                'order' => [
                    'id'          => $item->orderItem->merchantOrder->id,
                    'tracking_id' => $item->orderItem->merchantOrder->tracking_id,
                    'status_id'   => $item->orderItem->merchantOrder->status_id->value,
                ],
            ];

            $product             = $item->orderItem->product;
            $baseData['product'] = [
                'id'        => $product->id,
                'name'      => $product->name,
                'thumbnail' => $product->thumbnail,
                'price'     => $item->orderItem->price,
                'quantity'  => $item->orderItem->quantity,
                'sku'       => $item->orderItem->product_variant?->sku ?? $product->sku,
            ];

            if ($product->product_type_id == 2 && isset($product->variations)) {
                $baseData['product']['attributes'] = $product->variations->flatMap(function ($variation) {
                    return $variation->variationAttributes->map(function ($variationAttribute) {
                        return [
                            'attribute_id'        => (int) $variationAttribute->attribute_id,
                            'attribute_option_id' => (int) $variationAttribute->attribute_option_id,
                        ];
                    });
                });
            }

            return $baseData;
        });

        $newPaginator = new LengthAwarePaginator($restructuredData, $returnCases->total(), $returnCases->perPage(), $returnCases->currentPage(), [
            'path'  => $request->url(),
            'query' => $request->query(),
        ]);

        return ApiResponse::formatPagination('Return cases retrieved successfully', $newPaginator, Response::HTTP_OK);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $merchantId = $request->user()->merchant->id;

        $returnCase = OrderItemCase::with(['orderItem.product', 'orderItem.merchantOrder', 'reason', 'media'])
            ->where('type', 'return')
            ->whereHas('orderItem', function ($query) use ($merchantId) {
                $query->whereHas('merchantOrder', function ($subQuery) use ($merchantId) {
                    $subQuery->where('merchant_id', $merchantId);
                });
            })
            ->findOrFail($id);

        $baseData = [
            'id'               => $returnCase->id,
            'note'             => $returnCase->note,
            'merchant_note'    => $returnCase->merchant_note,
            'customer_name'    => $returnCase->orderItem->merchantOrder->order->customer_name,
            'customer_address' => $returnCase->orderItem->merchantOrder->order->customer_address,
            'customer_phone'   => $returnCase->orderItem->merchantOrder->order->customer_number,
            'type'             => $returnCase->type,
            'status'           => $returnCase->status,
            'created_at'       => $returnCase->created_at,
            'updated_at'       => $returnCase->updated_at,
            'reason'           => [
                'id'   => $returnCase->reason->id,
                'name' => $returnCase->reason->name,
            ],
            'order' => [
                'id'          => $returnCase->orderItem->merchantOrder->id,
                'tracking_id' => $returnCase->orderItem->merchantOrder->tracking_id,
                'status_id'   => $returnCase->orderItem->merchantOrder->status_id->value,
            ],
        ];

        $product             = $returnCase->orderItem->product;
        $baseData['product'] = [
            'id'        => $product->id,
            'name'      => $product->name,
            'thumbnail' => $product->thumbnail,
            'price'     => $returnCase->orderItem->price,
            'quantity'  => $returnCase->orderItem->quantity,
        ];

        if ($product->product_type_id == 2 && isset($product->variations)) {
            $baseData['product']['attributes'] = $product->variations->flatMap(function ($variation) {
                return $variation->variationAttributes->map(function ($variationAttribute) {
                    return [
                        'attribute_id'          => (int) $variationAttribute->attribute_id,
                        'attribute_name'        => $variationAttribute->attribute->name,
                        'attribute_option_id'   => (int) $variationAttribute->attribute_option_id,
                        'attribute_option_name' => $variationAttribute->attributeOption->name,
                    ];
                });
            });
        }

        $baseData['images'] = $returnCase->images;

        return ApiResponse::success('Return case retrieved successfully', $baseData, Response::HTTP_OK);
    }

    public function update(ReturnRequest $request, int $id): JsonResponse
    {
        $merchantId = $request->user()->merchant->id;
        // Validate input
        $validatedData = $request->validated();
        // Retrieve the return case with proper authorization check
        $returnCase = OrderItemCase::with(['orderItem.merchantOrder'])
            ->where('type', 'return')
            ->whereHas('orderItem.merchantOrder', fn($query) => $query->where('merchant_id', $merchantId))
            ->findOrFail($id);

        try {
            DB::beginTransaction();
            // Update allowed fields
            if (isset($validatedData['merchant_note'])) {
                $returnCase->merchant_note = $validatedData['merchant_note'];
            }
            // Process status update with validation
            if (isset($validatedData['status']) && $returnCase->status != $validatedData['status']) {
                $newStatus     = (int) $validatedData['status'];
                $currentStatus = $returnCase->status;

                Log::info('Status transition from %s to %s', [$currentStatus, $newStatus]);

                if (! $this->isValidStatusTransition($currentStatus, $newStatus)) {
                    throw new Exception('The status transition from ' . ucfirst(strtolower(ItemStatus::from($currentStatus)->name)) . ' to ' . ucfirst(strtolower(ItemStatus::from($newStatus)->name)) . ' is not allowed.');
                }

                $returnCase->status = $newStatus;
            }
            // Save changes
            $returnCase->save();

            if ($returnCase->status == ItemStatus::ACCEPTED->value) {

                $order = $returnCase->orderItem->merchantOrder;

                if (! $order->consignment_id) {
                    throw new Exception('Consignment ID not found for this order. This might be a fake/test order.');
                }

                $returnRequestId = self::steadfastReturnRequest($order->consignment_id, $validatedData['merchant_note'], $merchantId);

                Log::info('Return Request ID: ' . $returnRequestId);

                if (! empty($returnRequestId)) {
                    $order->return_request_id = $returnRequestId;
                    $order->save();
                }
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            return ApiResponse::error('Something went wrong', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return ApiResponse::success('Return case updated successfully', $returnCase, Response::HTTP_OK);
    }

    public static function steadfastReturnRequest(string $consignmentId, string $reason, int $merchantId)
    {
        $sfcConfig = ShopSetting::whereIn('key', ['sfc_base_url', 'sfc_public_key', 'sfc_secret_key'])->pluck('value', 'key')->toArray();

        if (empty($sfcConfig['sfc_base_url']) || empty($sfcConfig['sfc_public_key']) || empty($sfcConfig['sfc_secret_key'])) {
            DB::rollBack();

            return ApiResponse::failure('SFC Configuration problem, please contact admin', Response::HTTP_NOT_FOUND);
        }

        $response = Http::withHeaders([
            'api-key'    => $sfcConfig['sfc_public_key'],
            'secret-key' => $sfcConfig['sfc_secret_key'],
        ])->post($sfcConfig['sfc_base_url'] . '/create_return_request', [
            'consignment_id' => $consignmentId,
            'reason'         => $reason,
        ]);

        // Optional: Check response
        if ($response->successful()) {
            $data = json_decode($response->body(), true);

            return $data['id']; // or handle it as needed
        } else {
            return null;
        }
    }

    public function steadfastReturnStatus(Request $request, int $returnId)
    {
        $return = OrderItemCase::where('type', 'return')->find($returnId);

        if (! $return) {
            return ApiResponse::error('Return not found', Response::HTTP_NOT_FOUND);
        }

        $merchantId = $request->user()->merchant->id;
        $order      = MerchantOrder::find($return->orderItem->merchant_order_id);
        if (! $order || $order->merchant_id !== $merchantId) {
            return ApiResponse::error('Unauthorized access to this order', Response::HTTP_UNAUTHORIZED);
        }

        if (empty($order->return_request_id)) {
            return ApiResponse::error('No return request ID found for this order', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $sfcConfig = ShopSetting::whereIn('key', ['sfc_base_url', 'sfc_public_key', 'sfc_secret_key'])->pluck('value', 'key')->toArray();

        if (empty($sfcConfig['sfc_base_url']) || empty($sfcConfig['sfc_public_key']) || empty($sfcConfig['sfc_secret_key'])) {
            DB::rollBack();

            return ApiResponse::failure('SFC Configuration problem, please contact admin', Response::HTTP_NOT_FOUND);
        }

        $response = Http::withHeaders([
            'api-key'    => $sfcConfig['sfc_public_key'],
            'secret-key' => $sfcConfig['sfc_secret_key'],
        ])->get($sfcConfig['sfc_base_url'] . '/get_return_request/' . $order->return_request_id);

        if (! $response->successful()) {
            return ApiResponse::error('Failed to retrieve return request.', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = $response->json();

        $status = $data['data']['status'] ?? null;

        $statusMap = [
            'pending'    => ItemStatus::ACCEPTED->value,
            'processing' => ItemStatus::PROCESSING->value,
            'approved'   => ItemStatus::APPROVED->value,
            'completed'  => ItemStatus::COMPLETED->value,
            'cancelled'  => ItemStatus::CANCELLED->value,
        ];

        if (isset($statusMap[$status])) {
            $return->update(['status' => $statusMap[$status]]);
        }

        return ApiResponse::success('Return request status retrieved successfully', ['status' => $status], Response::HTTP_OK);
    }

    public function isValidStatusTransition(int $currentStatus, int $newStatus): bool
    {

        // Define valid transitions (from => [to])
        $validTransitions = [
            ItemStatus::PENDING->value => [
                ItemStatus::ACCEPTED->value,
                ItemStatus::REJECTED->value,
            ],
            ItemStatus::ACCEPTED->value   => [],
            ItemStatus::APPROVED->value   => [],
            ItemStatus::PROCESSING->value => [],
            ItemStatus::REFUNDED->value   => [],
            ItemStatus::COMPLETED->value  => [ItemStatus::REFUNDED->value],
            ItemStatus::CANCELLED->value  => [ItemStatus::REJECTED->value],
            ItemStatus::REJECTED->value   => [],
        ];

        $current = $currentStatus instanceof ItemStatus ? $currentStatus->value : $currentStatus;
        $new     = $newStatus instanceof ItemStatus ? $newStatus->value : $newStatus;

        return isset($validTransitions[$current])
            && in_array($new, $validTransitions[$current], true);
    }
}
