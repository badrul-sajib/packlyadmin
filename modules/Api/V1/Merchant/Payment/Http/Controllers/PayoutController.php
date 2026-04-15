<?php

namespace Modules\Api\V1\Merchant\Payment\Http\Controllers;

use App\Enums\OrderStatus;
use App\Enums\PayoutRequestStatus;
use App\Http\Controllers\Controller;
use App\Models\Merchant\MerchantOrder;
use App\Models\Payment\Payout;
use App\Models\Setting\ShopSetting;
use App\Services\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Api\V1\Merchant\Payment\Http\Requests\PayoutRequest;
use Modules\Api\V1\Merchant\Payment\Http\Resources\MerchantOrderResource;
use Symfony\Component\HttpFoundation\Response;

class PayoutController extends Controller
{
    public function __construct()
    {
        $this->middleware('shop.permission:show-payouts')->only('index', 'show', 'payoutAccounts');
        $this->middleware('shop.permission:create-payout')->only('store');
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->input('per_page', 10);
            $merchantId = Auth::user()->merchant->id;
            $shopSettings = $this->getShopSettings();

            $payouts = Payout::where('merchant_id', $merchantId)->with('payoutBeneficiary')->orderBy('created_at', 'desc')->paginate($perPage);

            $days = (int) $shopSettings['payout_request_date'];

            $merchantOrders = MerchantOrder::where([
                'merchant_id' => $merchantId,
                'status_id' => OrderStatus::DELIVERED->value,
                'payout_id' => null,
            ])
                ->where(function ($q) use ($days) {
                    $q->whereNotNull('delivered_at')
                        ->whereDate('delivered_at', '<=', now()->subDays($days))
                        ->orWhere(function ($q) use ($days) {
                            $q->whereNull('delivered_at')
                                ->whereDate('updated_at', '<=', now()->subDays($days));
                        });
                })
                ->with('items');

            $cloneMerchantOrders = clone $merchantOrders;

            $subtotal = $merchantOrders->sum('sub_total') - $cloneMerchantOrders->whereNull('bear_by_packly')->orWhere('bear_by_packly', '!=', 1)->sum('discount_amount');



            $gatewayCharge = 0;
            $totalCommission = 0;
            foreach ($merchantOrders->get() as $merchantOrder) {
                $gatewayCharge += $merchantOrder->gatewayCharge();
                $totalCommission += $merchantOrder->items()->where('status_id', OrderStatus::DELIVERED->value)->sum('commission');
            }

            $formattedPayouts = $payouts->map(function ($payout) {
                return [
                    'id' => $payout->id,
                    'request_id' => $payout->request_id,
                    'account_number' => $payout?->payoutBeneficiary?->account_number ?? null,
                    'bank' => $payout?->payoutBeneficiary?->getMobileWalletOrBankAttribute()?->name ?? null,
                    'amount' => $payout->amount,
                    'charge' => $payout->charge,
                    'status' => $payout->status,
                    'created_at' => $payout->created_at->format('Y-m-d H:i:A'),
                ];
            });

            $paginatedPayouts = new LengthAwarePaginator($formattedPayouts, $payouts->total(), $payouts->perPage(), $payouts->currentPage(), ['path' => $payouts->path()]);
            $response = ApiResponse::formatPagination('Payouts fetched successfully.', $paginatedPayouts, Response::HTTP_OK);
            $responseData = $response->getData(true);
            $responseData['available_balance'] = $subtotal;
            $responseData['subtotal'] = $subtotal;
            $responseData['total_commission'] = number_format((float) $totalCommission, 2, '.', '');
            $responseData['gateway_charge'] = $gatewayCharge;
            $responseData['gateway_charge_percent'] = $shopSettings['gateway_charge'];
            $responseData['withdrawal_amount'] = number_format($subtotal - $totalCommission - $gatewayCharge, 2, '.', '');
            $response->setData($responseData);

            return $response;
        } catch (\Exception $e) {
            return ApiResponse::error('Failed to retrieve payouts', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(PayoutRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $request->validated();

            $merchant = Auth::user()->merchant;
            $merchantId = $merchant->id;

            if ($merchant->payout_hold) {
                return ApiResponse::failure(
                    'Your payout requests are currently on hold. Please contact support for assistance.',
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $hasPendingPayout = Payout::where('merchant_id', $merchantId)
                ->where('status', PayoutRequestStatus::PENDING->value)
                ->exists();

            if ($hasPendingPayout) {
                return ApiResponse::failure(
                    'You have a pending payout request',
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $shopSettings = $this->getShopSettings();
            $days = (int) ($shopSettings['payout_request_date'] ?? 0);

            $merchantOrdersQuery = MerchantOrder::where([
                'merchant_id' => $merchantId,
                'status_id' => OrderStatus::DELIVERED->value,
                'payout_id' => null,
            ])->where(function ($q) use ($days) {
                $q->whereNotNull('delivered_at')
                    ->whereDate('delivered_at', '<=', now()->subDays($days))
                    ->orWhere(function ($q) use ($days) {
                        $q->whereNull('delivered_at')
                            ->whereDate('updated_at', '<=', now()->subDays($days));
                    });
            })->with('items');

            $merchantOrders = $merchantOrdersQuery->get();

            if ($merchantOrders->isEmpty()) {
                return ApiResponse::failure(
                    'No eligible orders found for payout',
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $subtotal = $merchantOrders->sum('sub_total')
                - $merchantOrders
                    ->where('bear_by_packly', '!=', 1)
                    ->sum('discount_amount');

            $gatewayCharge = 0;
            $totalCommission = 0;

            foreach ($merchantOrders as $order) {
                $gatewayCharge += $order->gatewayCharge();
                $totalCommission += $order->items
                    ->where('status_id', OrderStatus::DELIVERED->value)
                    ->sum('commission');
            }

            $withdrawalAmount = (float) (
                $subtotal - $totalCommission - $gatewayCharge
            );

            if (!$this->checkDailyLimit($merchantId, $shopSettings['per_day_request'])) {
                return ApiResponse::failure(
                    "You have reached the daily payout limit of {$shopSettings['per_day_request']}",
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $minAmount = $shopSettings['min_amount'] ?? 0;

            if ($withdrawalAmount < $minAmount) {
                return ApiResponse::failure(
                    "Minimum payout amount is {$minAmount}",
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $payout = Payout::create([
                'request_id' => $this->generatePayoutRequestId(),
                'merchant_id' => $merchantId,
                'payout_beneficiary_id' => (int) $request->payout_beneficiary_id,
                'amount' => $withdrawalAmount,
                'order_sub_total' => $subtotal,
                'order_commission' => $totalCommission,
                'gateway_fee' => $gatewayCharge,
                'charge' => $shopSettings['payout_charge'] ?? 0,
                'status' => PayoutRequestStatus::PENDING->value,
                'items' => $merchantOrders->toJson(),
            ]);

            $orderIds = $merchantOrders->pluck('id')->toArray();

            $payout->payoutMerchantOrders()->sync($orderIds);

            MerchantOrder::whereIn('id', $orderIds)
                ->update(['payout_id' => $payout->id]);

            DB::commit();

            return ApiResponse::successMessageForCreate(
                'Payout request created successfully',
                $payout,
                Response::HTTP_CREATED
            );

        } catch (\Throwable $e) {
            DB::rollBack();

            logger()->error('Payout create failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return ApiResponse::error(
                'Failed to create payout request',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    private function generatePayoutRequestId(): string
    {
        $prefix = 'PKLY-';

        do {
            // Generate random 8-digit number
            $randomNumber = random_int(10000000, 99999999);
            $requestId = $prefix . $randomNumber;

            // Check if already exists
            $exists = Payout::where('request_id', $requestId)->exists();

        } while ($exists);

        return $requestId;
    }

    private function getShopSettings(): array
    {
        $settings = ShopSetting::whereIn('key', ['per_day_request', 'min_amount', 'payout_charge', 'payout_request_date', 'gateway_charge'])->pluck('value', 'key');
        $merchantConfiguration = Auth::user()->merchant->configuration ?? null;

        if ($merchantConfiguration) {
            return [
                'per_day_request' => $merchantConfiguration->per_day_request ?? 1000,
                'min_amount' => $merchantConfiguration->min_amount ?? 0,
                'payout_charge' => $merchantConfiguration->payout_charge ?? 0,
                'payout_request_date' => $merchantConfiguration->payout_request_date ?? 3,
                'gateway_charge' => $merchantConfiguration->gateway_charge ?? 0,
            ];
        }

        return [
            'per_day_request' => $settings['per_day_request'] ?? 1000,
            'min_amount' => $settings['min_amount'] ?? 0,
            'payout_charge' => $settings['payout_charge'] ?? 0,
            'payout_request_date' => $settings['payout_request_date'] ?? 3,
            'gateway_charge' => $settings['gateway_charge'] ?? 0,
        ];
    }

    private function checkDailyLimit(int $merchantId, int $limit): bool
    {
        $todayCount = Payout::where('merchant_id', $merchantId)
            ->whereDate('created_at', now()->toDateString())
            ->count();

        return $todayCount < $limit;
    }

    public function show(int $id): JsonResponse
    {
        try {
            $payout = Payout::where('merchant_id', Auth::user()->merchant->id)
                ->with(['payoutBeneficiary', 'payoutMerchantOrders'])
                ->findOrFail($id);
            $shopSettings = ShopSetting::whereIn('key', ['gateway_charge', 'site_name', 'contact_email', 'contact_number', 'contact_address', 'support_number'])->pluck('value', 'key');

            return ApiResponse::success('Payout details retrieved successfully', [
                'id' => $payout->id,
                'company_info' => [
                    'name' => $shopSettings['site_name'],
                    'email' => $shopSettings['contact_email'],
                    'phone' => $shopSettings['contact_number'],
                    'address' => $shopSettings['contact_address'],
                    'support_number' => $shopSettings['support_number'],
                ],
                'bill_to' => [
                    'name' => Auth::user()->merchant->name,
                    'phone' => Auth::user()->merchant->phone,
                    'address' => Auth::user()->merchant->shop_address,
                ],
                'beneficiary_type' => $payout?->payoutBeneficiary?->beneficiaryTypes,
                'beneficiary_name' => $payout?->payoutBeneficiary?->account_holder_name,
                'beneficiary' => $payout?->payoutBeneficiary?->getMobileWalletOrBankAttribute(),
                'beneficiary_account' => $payout?->payoutBeneficiary?->account_number,
                'beneficiary_bank_branch' => $payout?->payoutBeneficiary?->branch_name,
                'beneficiary_bank_routing' => $payout?->payoutBeneficiary?->routing_number,
                'sub_total' => $payout->order_sub_total,
                'total_commission' => $payout->order_commission,
                'gateway_charge' => $payout->gateway_fee,
                'withdrawal_amount' => $payout->amount,
                'gateway_charge_percent' => $shopSettings['gateway_charge'],
                'request_id' => $payout->request_id,
                'status' => $payout->status,
                'created_at' => $payout->created_at->format('Y-m-d H:i:A'),
                'orders' => $payout->payoutMerchantOrders->map(function ($order) {
                    $commission = $order->items()->where('status_id', OrderStatus::DELIVERED->value)->sum('commission');
                    $gatewayCharge = $order->gatewayCharge();
                    $discountAmountPackly = intval($order->bear_by_packly == null ? $order->discount_amount : 0);
                    return [
                        'id' => $order->id,
                        'order_number' => $order->invoice_id ?? $order->order->invoice_id,
                        'sub_total' => $order->sub_total,
                        'commission' => $commission,
                        'gateway_charge' => $gatewayCharge,
                        'total' => ($order->sub_total - $commission - $gatewayCharge) - $discountAmountPackly,
                        'date' => $order->created_at->format('Y-m-d H:i:A'),
                    ];
                }),
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return ApiResponse::error('Failed to retrieve payout details', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function payoutEligibleMerchantOrders(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 10);

        try {
            $merchantId = Auth::user()->merchant->id;
            // Base query for eligible orders
            $baseQuery = MerchantOrder::where([
                'merchant_id' => $merchantId,
                'status_id' => OrderStatus::DELIVERED->value,
                'payout_id' => null,
            ]);

            // Search filter
            if ($request->has('search')) {
                $search = $request->input('search');
                $baseQuery->where(function ($query) use ($search) {
                    $query->whereHas('order', function ($orderQuery) use ($search) {
                        $orderQuery->where('customer_name', 'like', "%$search%")
                            ->orWhere('customer_number', 'like', "%$search%");
                    })
                        ->orWhere('tracking_id', 'like', "%$search%")
                        ->orWhere('invoice_id', 'like', "%$search%")
                        ->orWhereHas('payment', function ($paymentQuery) use ($search) {
                            $paymentQuery->where('tran_id', 'like', "%$search%");
                        });
                });
            }

            // Calculate totals
            $totals = (clone $baseQuery)->with('items')->get()->reduce(function ($carry, $order) {
                $items = $order->relationLoaded('items') ? $order->items : collect([]);

                $commission = $items->where('status_id', OrderStatus::DELIVERED->value)
                    ->sum('commission');

                $gatewayCharge = method_exists($order, 'gatewayCharge') ? $order->gatewayCharge() : 0;

                $amount = max(0, $order->sub_total - $commission - $gatewayCharge);

                $carry['total_commission'] += $commission;
                $carry['total_gateway_charge'] += $gatewayCharge;
                $carry['total_amount'] += $order->sub_total;
                $carry['total_payout_amount'] += $amount;

                return $carry;
            }, [
                'total_commission' => 0,
                'total_gateway_charge' => 0,
                'total_amount' => 0,
                'total_payout_amount' => 0,
            ]);

            // Paginate orders
            $merchantOrders = (clone $baseQuery)
                ->with('items', 'order', 'payment')
                ->paginate($perPage);

            return ApiResponse::formatPagination(
                'Merchant orders retrieved successfully',
                MerchantOrderResource::collection($merchantOrders),
                Response::HTTP_OK,
                $totals
            );

        } catch (\Exception $e) {
            return ApiResponse::error(
                'Failed to retrieve merchant orders: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function payoutMerchantOrders(Request $request): JsonResponse
    {
        try {
            $merchantId = Auth::user()->merchant->id;

            // Base query for eligible orders
            $baseQuery = MerchantOrder::where([
                'merchant_id' => $merchantId,
                'status_id' => OrderStatus::DELIVERED->value,
            ])->whereNotNull('payout_id');

            // Search filter
            if ($request->has('search')) {
                $search = $request->input('search');
                $baseQuery->where(function ($query) use ($search) {
                    $query->whereHas('order', function ($orderQuery) use ($search) {
                        $orderQuery->where('customer_name', 'like', "%$search%")
                            ->orWhere('customer_number', 'like', "%$search%");
                    })
                        ->orWhere('tracking_id', 'like', "%$search%")
                        ->orWhere('invoice_id', 'like', "%$search%")
                        ->orWhereHas('payment', function ($paymentQuery) use ($search) {
                            $paymentQuery->where('tran_id', 'like', "%$search%");
                        });
                });
            }

            // Calculate totals
            $totals = (clone $baseQuery)->with('items')->get()->reduce(function ($carry, $order) {
                $items = $order->relationLoaded('items') ? $order->items : collect([]);

                $commission = $items->where('status_id', OrderStatus::DELIVERED->value)
                    ->sum('commission');

                $gatewayCharge = method_exists($order, 'gatewayCharge') ? $order->gatewayCharge() : 0;

                $amount = max(0, $order->sub_total - $commission - $gatewayCharge);

                $carry['total_commission'] += $commission;
                $carry['total_gateway_charge'] += $gatewayCharge;
                $carry['total_amount'] += $order->sub_total;
                $carry['total_payout_amount'] += $amount;

                return $carry;
            }, [
                'total_commission' => 0,
                'total_gateway_charge' => 0,
                'total_amount' => 0,
                'total_payout_amount' => 0,
            ]);

            // Paginate orders
            $merchantOrders = (clone $baseQuery)
                ->with('items', 'order', 'payment')
                ->paginate($request->input('per_page', 10));

            return ApiResponse::formatPagination(
                'Merchant orders retrieved successfully',
                MerchantOrderResource::collection($merchantOrders),
                Response::HTTP_OK,
                $totals
            );

        } catch (\Exception $e) {
            return ApiResponse::error(
                'Failed to retrieve merchant orders: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
