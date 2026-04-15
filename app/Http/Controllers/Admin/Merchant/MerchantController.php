<?php

namespace App\Http\Controllers\Admin\Merchant;

use Exception;
use Throwable;
use Carbon\Carbon;
use App\Models\User\Otp;
use App\Services\SmsService;
use Illuminate\Http\Request;
use App\Services\ApiResponse;
use App\Enums\ShopProductStatus;
use App\Services\ProductService;
use App\Models\Category\Category;
use App\Models\Merchant\Merchant;
use Illuminate\Http\JsonResponse;
use App\Enums\PayoutRequestStatus;
use Illuminate\Support\Facades\Log;
use App\Actions\FetchMerchantOrders;
use App\Http\Controllers\Controller;
use App\Models\Category\SubCategory;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\RedirectResponse;
use App\Services\PayoutRequestService;
use App\Models\Merchant\MerchantSetting;
use App\Models\Category\SubCategoryChild;
use App\Services\Merchant\MerchantService;
use App\Http\Requests\Admin\MerchantRequest;
use App\Enums\OrderStatus;
use App\Models\Merchant\MerchantOrder;
use App\Models\Payment\Payout;
use App\Models\Payment\PayoutBeneficiary;
use App\Models\Payment\PayoutBeneficiaryBank;
use App\Models\Setting\ShopSetting;
use App\Services\Common\ProductImportService;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Payment\PayoutBeneficiaryMobileWallet;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Api\V1\Merchant\Product\Http\Requests\ProductCsvRowRequest;
use Modules\Api\V1\Merchant\Product\Http\Requests\BulkProductCsvRequest;


class MerchantController extends Controller
{
    public function __construct(private readonly MerchantService $merchantService)
    {
        $this->middleware('permission:merchant-list')->only('index');
        $this->middleware('permission:merchant-show')->only('show');
        $this->middleware('permission:merchant-active')->only('active');
        $this->middleware('permission:merchant-inactive')->only('inactive');
        $this->middleware('permission:merchant-balance-list')->only('balance');
        $this->middleware('permission:merchant-payout-hold')->only('togglePayoutHold');
        $this->middleware('permission:manual-payout-create')->only('previewManualPayout', 'storeManualPayout');
    }

    /**
     * @throws Throwable
     */
    public function index(Request $request)
    {
        $merchants = $this->merchantService->getAllMerchant($request);

        if ($request->ajax()) {
            return view('components.merchant.table', ['entity' => $merchants])->render();
        }

        return view('Admin::merchant.index', compact('merchants'));
    }

    public function show(Request $request, string $id, FetchMerchantOrders $fetchMerchantOrders)
    {
        try {

            $request->merge(['merchant_id' => $id]);

            if ($request->ajax() && $request->product_type == 'inventory') {
                $products = ProductService::inventoryProducts($request);

                return view('components.merchant.offline_product_table', ['products' => $products])->render();
            }

            if ($request->ajax() && $request->product_type == 'e-commerce-pending') {
                return $result = app(\App\Http\Controllers\Admin\Product\ProductChangeRequestController::class)->index();
            }

            $products = ProductService::requestProductsForMerchant($request);
            if ($request->ajax() && $request->product_type != 'inventory') {
                request()->merge(['merchant_id' => $id]);
                return view('components.merchant.product_table', ['entity' => $products])->render();
            }

            $shopStatuses = ShopProductStatus::label();
            $merchant = $this->merchantService->getMerchantById($id);

            $merchant->setAttribute(
                'userRelation',
                $merchant->userRelation->first()
            );

            $orders = $fetchMerchantOrders->executeMerchantOrders($request);

            // count product and orders
            $count = $this->merchantService->getCount($id);

            $updated_settings = MerchantSetting::where('merchant_id', $id)
                ->where('key', 'shop_settings')
                ->first() ?? null;

            $shopData = json_decode(json_encode(json_decode($updated_settings?->value, true)));

            $activities = $merchant->activities()
                ->with('causer')
                ->orderBy('created_at', 'desc')
                ->get();

            $categories = Category::active()->select('id', 'name')->get();
            $sub_categories = SubCategory::active()->select('id', 'name')->get();
            $child_categories = SubCategoryChild::active()->select('id', 'name')->get();

            $request->merge(['status' => '']);
            $request->merge(['perPage' => 25]);
            $payoutRequests = (new PayoutRequestService)->getPayoutRequests($request);

            return view('Admin::merchant.show', compact('merchant', 'activities', 'shopData', 'products', 'shopStatuses', 'orders', 'count', 'categories', 'sub_categories', 'child_categories', 'payoutRequests'));
        } catch (ModelNotFoundException $e) {
            Log::error('Error fetching merchant details');

            return redirect()->back()->with(['error' => 'Merchant not found.']);
        } catch (Exception $e) {
            Log::error('Unexpected error' . $e->getMessage());

            return redirect()->back()->with(['error' => 'Something went wrong. Please try again.']);
        }
    }

    public function create()
    {
        return view('Admin::merchant.create');
    }

    /**
     * @throws Throwable
     */
    public function store(MerchantRequest $request)
    {
        $this->merchantService->createMerchant($request);

        return success('Merchant created successfully');
    }

    public function ajaxMerchants(Request $request)
    {
        $merchants = MerchantService::getMerchantBySearch($request);

        return success('Merchant fetched successfully', $merchants);
    }

    public function active(int $id)
    {
        $this->merchantService->activeMerchant($id);

        return redirect()->back();
    }

    public function resetPassword(Merchant $merchant)
    {
        return $this->merchantService->resetPassword($merchant);
    }

    public function autoApprove(Request $request)
    {
        $merchant = Merchant::find($request->merchant_id);

        if ($merchant) {
            $oldStatus = $merchant->auto_approve == '1' ? 'Enabled' : 'Disabled';

            $merchant->auto_approve = $request->auto_approve ? 1 : 0;

            $newStatus = $merchant->auto_approve == '1' ? 'Enabled' : 'Disabled';

            activity()
                ->useLog('product-auto-update')
                ->event('updated')
                ->performedOn($merchant)
                ->causedBy(auth()->user())
                ->withProperties([
                    'old' => $oldStatus,
                    'new' => $newStatus,
                ])
                ->log('Product auto approved status updated by ' . auth()->user()->name);

            $merchant->save();

            return response()->json(['success' => true, 'message' => 'Merchant approval status updated!']);
        }

        return response()->json(['success' => false, 'message' => 'Merchant not found!'], 404);
    }

    public function togglePayoutHold(Request $request): JsonResponse
    {
        $merchant = Merchant::find($request->merchant_id);

        if (!$merchant) {
            return response()->json(['success' => false, 'message' => 'Merchant not found!'], 404);
        }

        $merchant->payout_hold = $request->payout_hold ? 1 : 0;
        $merchant->save();

        $label = $merchant->payout_hold ? 'held' : 'released';

        activity()
            ->useLog('payout-hold')
            ->event('updated')
            ->performedOn($merchant)
            ->causedBy(auth()->user())
            ->withProperties(['payout_hold' => $merchant->payout_hold])
            ->log("Payout {$label} by " . auth()->user()->name);

        $message = $merchant->payout_hold
            ? 'Payout hold enabled. Merchant cannot create payout requests.'
            : 'Payout hold released. Merchant can now create payout requests.';

        return response()->json(['success' => true, 'message' => $message]);
    }

    public function generateToken(Merchant $merchant)
    {
        $token = $merchant->user?->createToken('merchant-token')->plainTextToken;

        return response()->json(['token' => $token]);
    }

    public function contactChange(Request $request, Merchant $merchant): RedirectResponse
    {
        try {
            if ($request->filled('phone_number')) {
                $this->merchantService->phoneNumberChange($request, $merchant);
                $message = 'Phone number changed successfully';
            } else {
                $this->merchantService->emailChange($request, $merchant);
                $message = 'Email changed successfully';
            }

            return redirect()->back()->with('success', $message);
        } catch (\Illuminate\Database\QueryException $e) {
            if (isset($e->errorInfo[1]) && $e->errorInfo[1] == 1062) {
                if ($request->filled('phone_number')) {
                    return redirect()->back()->with('error', 'This phone number is already registered to another merchant. Please use a unique phone number.');
                } else {
                    return redirect()->back()->with('error', 'This email is already taken.');
                }
            }

            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function otpSend(Request $request): JsonResponse
    {
        $merchant = Merchant::with('userRelation')->find($request->merchant_id);
        if (!$merchant) {
            return response()->json(['error' => 'Merchant not found!'], 404);
        }

        $code = rand(100000, 999999);
        $expiresAt = Carbon::now()->addMinutes(2);

        $isPhone = $request->otp_target == 'phone';
        $targetValue = $isPhone ? $merchant->user->phone : $merchant->user->email;

        Otp::updateOrCreate(
            [$isPhone ? 'phone' : 'email' => $targetValue],
            [
                'otp' => $code,
                'expires_at' => $expiresAt,
                'is_verified' => 0,
            ]
        );

        if ($isPhone) {
            (new SmsService)->sendMessage($targetValue, "From Packly.com: Your OTP is {$code}. It expires in 2 minutes.");
        } else {
            $subject = 'Packly.com - Email Verification Code';
            $body = "Hello,\n\nYour OTP is: {$code}\n\nThis code will expire in 2 minutes.\n\nThanks,\nPackly.com";

            Mail::raw($body, function ($message) use ($targetValue, $subject) {
                $message->to($targetValue)->subject($subject);
            });
        }

        return response()->json(['success' => 'OTP sent successfully!']);
    }



    public function otpVerify(Request $request): JsonResponse
    {
        $merchant = Merchant::with('userRelation')->find($request->merchant_id);
        if (!$merchant) {
            return response()->json(['error' => 'Merchant not found!'], 404);
        }
        $user = $merchant->userRelation->first() ?? null;

        $isPhone = $request->otp_target === 'phone';
        $targetValue = $isPhone ? $user->phone : $user->email;

        $otp = Otp::where($isPhone ? 'phone' : 'email', $targetValue)
            ->where('otp', $request->otp)
            ->where('is_verified', 0)
            ->first();

        if (!$otp) {
            return response()->json(['error' => 'Invalid OTP!'], 400);
        }

        if ($otp->expires_at && Carbon::now()->greaterThan($otp->expires_at)) {
            return response()->json(['error' => 'OTP expired!'], 400);
        }

        $otp->update([
            'expires_at' => null,
            'is_verified' => 1,
        ]);

        return response()->json(['success' => 'OTP verified successfully!']);
    }

    public function productUpload(Request $request, Merchant $merchant): JsonResponse
    {
        try {
            // Validate file input
            if (!$request->hasFile('product_file')) {
                return ApiResponse::failure('No file uploaded.', Response::HTTP_BAD_REQUEST);
            }

            $file = $request->file('product_file');

            // Load Excel file as array
            $dataRows = Excel::toArray([], $file)[0]; // First sheet only

            if (empty($dataRows) || !isset($dataRows[0])) {
                return ApiResponse::failure('Excel file is empty or invalid.', Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            // Assume the first row is the header
            $headers = array_map('strtolower', $dataRows[0]);
            unset($dataRows[0]);

            // Map each row to structured product format
            $timestamp = now()->timestamp;
            $products = [];

            foreach (array_values($dataRows) as $index => $row) {
                $rowData = array_combine($headers, $row);

                $products[] = [
                    '_tempId' => "row-{$index}-{$timestamp}",
                    'name' => $rowData['name'] ?? null,
                    'category_name' => $rowData['category'] ?? null,
                    'sub_category_name' => $rowData['subcategory'] ?? null,
                    'sub_child_category_name' => $rowData['subchildcategory'] ?? null,
                    'brand_name' => $rowData['brand'] ?? null,
                    'unit_name' => $rowData['unit'] ?? null,
                    'product_type' => $rowData['producttype'] ?? 'single',
                    'description' => $rowData['description'] ?? null,
                    'specification' => $rowData['specification'] ?? null,
                    'selling_type' => $rowData['sellingtype'] ?? 'retail',
                    'purchase_price' => (float) ($rowData['purchaseprice'] ?? 0),
                    'regular_price' => (float) ($rowData['regularprice'] ?? 0),
                    'discount_price' => (float) ($rowData['discountprice'] ?? 0),
                    'wholesale_price' => (float) ($rowData['wholesaleprice'] ?? 0),
                    'minimum_qty' => (int) ($rowData['minimumqty'] ?? 0),
                    'opening_stock' => (int) ($rowData['openingstock'] ?? 0),
                ];
            }

            $data = ['products' => $products];

            $rowRules = (new ProductCsvRowRequest)->rules();
            $bulkRules = (new BulkProductCsvRequest)->rules();
            $merchantId = $merchant->id;
            $paymentDate = $request->payment_date ?? null;

            return (new ProductImportService)->processImport($data, $rowRules, $bulkRules, $merchantId, $paymentDate);
        } catch (Throwable $th) {
            return response()->json(['success' => false, 'message' => 'Something went wrong'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function assignKam(Request $request, Merchant $merchant): JsonResponse
    {
        $request->validate([
            'admin_id' => 'required',
        ]);

        try {
            $merchant->update([
                'admin_id' => $request->admin_id,
            ]);

            return response()->json(['success' => true, 'message' => 'KAM assigned successfully'], Response::HTTP_OK);
        } catch (Throwable $th) {
            return response()->json(['success' => false, 'message' => 'Something went wrong'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function balance(Request $request)
    {
        $merchants = $this->merchantService->getAllMerchant($request);

        if ($request->ajax()) {
            return view('components.merchant.balance_table', ['entity' => $merchants])->render();
        }

        return view('Admin::merchant.balance', compact('merchants'));
    }

    public function beneficiaries(Merchant $merchant): JsonResponse
    {
        $beneficiaries = PayoutBeneficiary::where('merchant_id', $merchant->id)
            ->with(['beneficiaryTypes', 'mobileWallet', 'bank'])
            ->get()
            ->map(function ($beneficiary) {
                return [
                    'id'                 => $beneficiary->id,
                    'beneficiary_type'   => $beneficiary->beneficiaryTypes->name ?? 'N/A',
                    'beneficiary_name'   => $beneficiary->account_holder_name ?? 'N/A',
                    'beneficiary'        => $beneficiary->getMobileWalletOrBankAttribute() ? [
                        'id'   => $beneficiary->getMobileWalletOrBankAttribute()->id,
                        'name' => $beneficiary->getMobileWalletOrBankAttribute()->name,
                    ] : null,
                    'beneficiary_account' => $beneficiary->account_number ?? 'N/A',
                    'beneficiary_branch'  => $beneficiary->branch_name ?? 'N/A',
                    'routing_number'      => $beneficiary->routing_number ?? 'N/A',
                    'is_default'          => (bool) $beneficiary->is_default,
                ];
            });

        return response()->json($beneficiaries);
    }

    public function orders(Request $request, Merchant $merchant)
    {
        if (!$request->filled('start_date') && !$request->filled('end_date')) {
            $request->merge([
                'start_date' => now()->toDateString(),
                'end_date' => now()->toDateString(),
            ]);
        }

        $request->merge(['merchant_id' => $merchant->id]);

        $orders = (new FetchMerchantOrders)->execute($request);

        if ($request->ajax()) {
            return view('components.orders.merchant_table', ['entity' => $orders])->render();
        }

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        return view('Admin::merchant.orders', compact('orders', 'startDate', 'endDate', 'merchant'));
    }

    public function reports(Request $request, Merchant $merchant)
    {

        $reports = $this->merchantService->getMerchantReports($request, $merchant);

        if ($request->ajax()) {
            return view('components.merchant.report_table', ['entity' => $reports])->render();
        }

        return view('Admin::merchant.reports', compact('reports', 'merchant'));
    }

    public function payouts(Request $request, Merchant $merchant)
    {
        $request->merge(['merchant_id' => $merchant->id]);

        $payouts = (new PayoutRequestService)->getPayoutRequests($request);

        $filters = [
            ['label' => 'All', 'value' => 'all'],
            [
                'label' => 'Cash',
                'value' => null,
            ],
            [
                'label' => 'Bank',
                'value' => PayoutBeneficiaryBank::where('status', 1)->pluck('id')->toArray(),
            ],
            [
                'label' => 'Bkash/Nagad/Rocket',
                'value' => PayoutBeneficiaryMobileWallet::where('status', 1)->pluck('id')->toArray(),
            ],
            [
                'label' => 'Bkash',
                'value' => 1,
            ],
            [
                'label' => 'Nagad',
                'value' => 2,
            ],
            [
                'label' => 'Rocket',
                'value' => 3,
            ],
        ];

        $statuses = PayoutRequestStatus::cases();

        if ($request->ajax()) {
            return view('components.payout_request.table', ['entity' => $payouts])->render();
        }

        return view('Admin::merchant.payouts', compact('payouts', 'filters', 'statuses', 'merchant'));
    }

    public function payoutOrders(Request $request, Merchant $merchant)
    {
        $request->merge(['merchant_id' => $merchant->id]);

        $orders = (new FetchMerchantOrders)->payoutOrders($request, $merchant);

        if ($request->ajax()) {
            return view('components.orders.payout_table', ['entity' => $orders])->render();
        }

        $beneficiaries = PayoutBeneficiary::where('merchant_id', $merchant->id)
            ->with(['beneficiaryTypes', 'mobileWallet', 'bank'])
            ->orderByDesc('is_default')
            ->get();

        return view('Admin::merchant.payout_orders', compact('orders', 'merchant', 'beneficiaries'));
    }

    public function previewManualPayout(Request $request, Merchant $merchant): JsonResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $requestedAmount = (float) $request->amount;
        $shopSettings = $this->getMerchantShopSettings($merchant);
        $days = (int) $shopSettings['payout_request_date'];

        $eligibleOrders = MerchantOrder::where([
            'merchant_id' => $merchant->id,
            'status_id'   => OrderStatus::DELIVERED->value,
            'payout_id'   => null,
        ])
            ->where(function ($q) use ($days) {
                $q->where(function ($q) use ($days) {
                    $q->whereNotNull('delivered_at')
                        ->whereDate('delivered_at', '<=', now()->subDays($days));
                })->orWhere(function ($q) use ($days) {
                    $q->whereNull('delivered_at')
                        ->whereDate('updated_at', '<=', now()->subDays($days));
                });
            })
            ->with('items')
            ->get();

        if ($eligibleOrders->isEmpty()) {
            return response()->json(['error' => 'No eligible orders found for this merchant.'], 422);
        }

        $ordersWithNet = $eligibleOrders->map(function ($order) {
            $commission    = $order->items->where('status_id', OrderStatus::DELIVERED->value)->sum('commission');
            $gatewayCharge = $order->gatewayCharge();
            $discount      = ($order->bear_by_packly != 1) ? (float) $order->discount_amount : 0.0;
            $netAmount     = (float) $order->sub_total - $commission - $gatewayCharge - $discount;
            return [
                'order'         => $order,
                'commission'    => $commission,
                'gateway_charge'=> $gatewayCharge,
                'discount'      => $discount,
                'net_amount'    => $netAmount,
            ];
        })->values();

        $netAmounts = $ordersWithNet->pluck('net_amount')->toArray();
        $result     = $this->findClosestOrders($netAmounts, $requestedAmount);

        if (empty($result['indices'])) {
            return response()->json(['error' => 'No eligible orders found for this merchant.'], 422);
        }

        $selectedOrders = [];
        $accumulated    = 0.0;

        foreach ($result['indices'] as $idx) {
            $row = $ordersWithNet[$idx];
            $selectedOrders[] = [
                'id'             => $row['order']->id,
                'invoice_id'     => $row['order']->invoice_id ?? $row['order']->order?->invoice_id,
                'sub_total'      => $row['order']->sub_total,
                'commission'     => round($row['commission'], 2),
                'gateway_charge' => round($row['gateway_charge'], 2),
                'discount'       => round($row['discount'], 2),
                'net_amount'     => round($row['net_amount'], 2),
            ];
            $accumulated += $row['net_amount'];
        }

        $beneficiaries = PayoutBeneficiary::where('merchant_id', $merchant->id)
            ->with(['beneficiaryTypes', 'mobileWallet', 'bank'])
            ->orderByDesc('is_default')
            ->get()
            ->map(fn ($b) => [
                'id'     => $b->id,
                'label'  => ($b->beneficiaryTypes->name ?? 'N/A') . ' — ' . ($b->getMobileWalletOrBankAttribute()?->name ?? 'N/A') . ' (' . ($b->account_number ?? 'N/A') . ')',
                'is_default' => (bool) $b->is_default,
            ]);

        return response()->json([
            'requested_amount' => round($requestedAmount, 2),
            'payout_amount'    => round($accumulated, 2),
            'total_net'        => round($accumulated, 2),
            'orders'           => $selectedOrders,
            'beneficiaries'    => $beneficiaries,
        ]);
    }

    public function storeManualPayout(Request $request, Merchant $merchant): JsonResponse
    {
        $request->validate([
            'amount'                => 'required|numeric|min:1',
            'payout_beneficiary_id' => 'required|exists:payout_beneficiaries,id',
        ]);

        DB::beginTransaction();

        try {
            $requestedAmount = (float) $request->amount;
            $shopSettings = $this->getMerchantShopSettings($merchant);
            $days = (int) $shopSettings['payout_request_date'];

            $eligibleOrders = MerchantOrder::where([
                'merchant_id' => $merchant->id,
                'status_id'   => OrderStatus::DELIVERED->value,
                'payout_id'   => null,
            ])
                ->where(function ($q) use ($days) {
                    $q->where(function ($q) use ($days) {
                        $q->whereNotNull('delivered_at')
                            ->whereDate('delivered_at', '<=', now()->subDays($days));
                    })->orWhere(function ($q) use ($days) {
                        $q->whereNull('delivered_at')
                            ->whereDate('updated_at', '<=', now()->subDays($days));
                    });
                })
                ->with('items')
                ->get();

            if ($eligibleOrders->isEmpty()) {
                DB::rollBack();
                return response()->json(['error' => 'No eligible orders found.'], 422);
            }

            $ordersWithNet = $eligibleOrders->map(function ($order) {
                $commission    = $order->items->where('status_id', OrderStatus::DELIVERED->value)->sum('commission');
                $gatewayCharge = $order->gatewayCharge();
                $discount      = ($order->bear_by_packly != 1) ? (float) $order->discount_amount : 0.0;
                $netAmount     = (float) $order->sub_total - $commission - $gatewayCharge - $discount;
                return [
                    'order'         => $order,
                    'commission'    => $commission,
                    'gateway_charge'=> $gatewayCharge,
                    'discount'      => $discount,
                    'net_amount'    => $netAmount,
                ];
            })->values();

            $netAmounts = $ordersWithNet->pluck('net_amount')->toArray();
            $result     = $this->findClosestOrders($netAmounts, $requestedAmount);

            if (empty($result['indices'])) {
                DB::rollBack();
                return response()->json(['error' => 'No eligible orders found.'], 422);
            }

            $selectedOrders     = collect();
            $accumulated        = 0.0;
            $subtotal           = 0.0;
            $totalCommission    = 0.0;
            $totalGatewayCharge = 0.0;

            foreach ($result['indices'] as $idx) {
                $row = $ordersWithNet[$idx];
                $selectedOrders->push($row['order']);
                $subtotal           += (float) $row['order']->sub_total - $row['discount'];
                $totalCommission    += $row['commission'];
                $totalGatewayCharge += $row['gateway_charge'];
                $accumulated        += $row['net_amount'];
            }

            $payoutAmount = $accumulated;

            $requestId = $this->generateManualPayoutRequestId();

            $payout = Payout::create([
                'request_id'            => $requestId,
                'merchant_id'           => $merchant->id,
                'payout_beneficiary_id' => (int) $request->payout_beneficiary_id,
                'amount'                => round($payoutAmount, 2),
                'order_sub_total'       => round($subtotal, 2),
                'order_commission'      => round($totalCommission, 2),
                'gateway_fee'           => round($totalGatewayCharge, 2),
                'charge'                => $shopSettings['payout_charge'] ?? 0,
                'status'                => \App\Enums\PayoutRequestStatus::PENDING->value,
                'items'                 => $selectedOrders->toJson(),
                'created_by'            => auth()->id(),
            ]);

            $orderIds = $selectedOrders->pluck('id')->toArray();
            $payout->payoutMerchantOrders()->sync($orderIds);
            MerchantOrder::whereIn('id', $orderIds)->update(['payout_id' => $payout->id]);

            DB::commit();

            return response()->json([
                'success'    => true,
                'message'    => 'Manual payout request created successfully.',
                'request_id' => $payout->request_id,
                'redirect'   => route('admin.payout-requests.show', $payout->request_id),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            logger()->error('Manual payout creation failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to create manual payout. Please try again.'], 500);
        }
    }

    private function findClosestOrders(array $netAmounts, float $target): array
    {
        $bestIndices = [];
        $closestSum  = PHP_INT_MAX;
        $n           = count($netAmounts);
        $netAmounts  = array_values($netAmounts);

        $backtrack = function (int $index, array $currentIndices, float $currentSum) use (
            &$netAmounts, $target, &$bestIndices, &$closestSum, &$backtrack, $n
        ): void {
            if (abs($target - $currentSum) < abs($target - $closestSum)) {
                $closestSum  = $currentSum;
                $bestIndices = $currentIndices;
            }

            if ($currentSum == $target) {
                return;
            }

            for ($i = $index; $i < $n; $i++) {
                if ($currentSum + $netAmounts[$i] > $target * 2) {
                    continue;
                }

                $backtrack(
                    $i + 1,
                    array_merge($currentIndices, [$i]),
                    $currentSum + $netAmounts[$i]
                );
            }
        };

        $backtrack(0, [], 0.0);

        return [
            'indices'    => $bestIndices,
            'sum'        => $closestSum,
            'difference' => abs($target - $closestSum),
        ];
    }

    private function getMerchantShopSettings(Merchant $merchant): array
    {
        $defaults = [
            'per_day_request'    => 1000,
            'min_amount'         => 0,
            'payout_charge'      => 0,
            'payout_request_date' => 3,
            'gateway_charge'     => 0,
        ];

        if ($merchant->configuration) {
            return [
                'per_day_request'    => $merchant->configuration->per_day_request ?? $defaults['per_day_request'],
                'min_amount'         => $merchant->configuration->min_amount ?? $defaults['min_amount'],
                'payout_charge'      => $merchant->configuration->payout_charge ?? $defaults['payout_charge'],
                'payout_request_date' => $merchant->configuration->payout_request_date ?? $defaults['payout_request_date'],
                'gateway_charge'     => $merchant->configuration->gateway_charge ?? $defaults['gateway_charge'],
            ];
        }

        $settings = ShopSetting::whereIn('key', array_keys($defaults))
            ->pluck('value', 'key')
            ->toArray();

        return array_merge($defaults, $settings);
    }

    private function generateManualPayoutRequestId(): string
    {
        do {
            $requestId = 'PKLY-' . random_int(10000000, 99999999);
        } while (Payout::where('request_id', $requestId)->exists());

        return $requestId;
    }
}
