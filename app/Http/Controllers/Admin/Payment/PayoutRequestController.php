<?php

namespace App\Http\Controllers\Admin\Payment;

use App\Enums\PayoutRequestStatus;
use App\Exports\PayoutReportExport;
use App\Exports\MFSPayoutExport;
use App\Exports\ExportPayoutRequests;
use App\Http\Controllers\Controller;
use App\Models\Payment\PaidBySfc;
use App\Models\Payment\Payout;
use App\Models\Setting\ShopSetting;
use App\Services\PayoutRequestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Payment\PayoutBeneficiary;
use Throwable;
use Maatwebsite\Excel\Facades\Excel;

class PayoutRequestController extends Controller
{
    protected PayoutRequestService $payoutRequestService;

    public function __construct(PayoutRequestService $payoutRequestService)
    {
        $this->payoutRequestService = $payoutRequestService;
        $this->middleware('permission:payout-request-list')->only(['index', 'export']);
        $this->middleware('permission:payout-request-show')->only(['show', 'invoice']);
        $this->middleware('permission:payout-request-update')->only(['statusUpdate', 'updateBeneficiary', 'bulkUpdate', 'getPaidBySfc']);
        $this->middleware('permission:payout-request-today-list')->only('todaysPayout');
        $this->middleware('permission:payout-request-report')->only(['report', 'reportDetails']);
        $this->middleware('permission:payout-request-paid-by-sfc-list')->only(['paidBySfc', 'paidBySfcDetails']);
    }

    public function index(Request $request)
    {
        $data = $this->payoutRequestService->getPayoutRequests($request);
        $balanceSummary = null;
        $statusParam = (string) $request->input('status', '');
        $statusValue = $statusParam !== '' ? (int) $statusParam : null;
        $showBalanceStatuses = [
            PayoutRequestStatus::PENDING->value,
            PayoutRequestStatus::READY->value,
        ];
        if ($statusValue !== null && in_array($statusValue, $showBalanceStatuses, true)) {
            $balanceSummary = $this->payoutRequestService->getPayoutBalanceSummary($request, $statusValue);
            $balanceSummary['status_label'] = PayoutRequestStatus::from($statusValue)->label();
        }
        $filters = [
            'all' => ['label' => 'All'],
            'bank' => ['label' => 'Bank'],
            'mobile_all' => ['label' => 'Bkash/Nagad/Rocket'],
            'bkash' => ['label' => 'Bkash'],
            'nagad' => ['label' => 'Nagad'],
            'rocket' => ['label' => 'Rocket'],
        ];
        $statuses = PayoutRequestStatus::cases();
        if ($request->ajax()) {
            return view('components.payout_request.table', ['entity' => $data])->render();
        }
        return customView(
            ['ajax' => 'components.payout_request.table', 'default' => 'Admin::payout_requests.index'],
            ['entity' => $data, 'filters' => $filters, 'statuses' => $statuses, 'balanceSummary' => $balanceSummary]
        );
    }

    public function show(Request $request, $request_id)
    {
        try {
            $data = Payout::with(['merchant', 'payoutBeneficiary', 'payoutMerchantOrders', 'paidBy', 'readyBy', 'heldBy', 'payoutMethodChangedBy', 'createdBy'])
                ->where('request_id', $request_id)
                ->first()
                ?? Payout::with(['merchant', 'payoutBeneficiary', 'payoutMerchantOrders', 'paidBy', 'readyBy', 'heldBy', 'payoutMethodChangedBy', 'createdBy'])
                ->findOrFail($request_id);

            $perPage = $request->input('perPage', 10);
            $page = $request->input('page', 1);
            $statuses = PayoutRequestStatus::cases();

            $orders = rescue(function () use ($data, $perPage, $page) {
                return $data->payoutMerchantOrders()->orderBy('created_at', 'asc')->paginate($perPage, ['*'], 'page', $page);
            }, []);

            // Fetch and format beneficiaries on page load (no AJAX needed)
            $beneficiariesQuery = PayoutBeneficiary::where('merchant_id', $data->merchant->id)
                ->with(['beneficiaryTypes', 'mobileWallet', 'bank']);

            $beneficiaries = $beneficiariesQuery->get()->map(function ($beneficiary) {
                return [
                    'id' => $beneficiary->id,
                    'beneficiary_type' => $beneficiary->beneficiaryTypes->name ?? 'N/A',
                    'beneficiary_name' => $beneficiary->account_holder_name ?? 'N/A',
                    'beneficiary' => $beneficiary->getMobileWalletOrBankAttribute() ? [
                        'id' => $beneficiary->getMobileWalletOrBankAttribute()->id,
                        'name' => $beneficiary->getMobileWalletOrBankAttribute()->name,
                    ] : null,
                    'beneficiary_account' => $beneficiary->account_number ?? 'N/A',
                    'beneficiary_branch' => $beneficiary->branch_name ?? 'N/A',
                    'routing_number' => $beneficiary->routing_number ?? 'N/A',
                    'is_default' => (bool) $beneficiary->is_default,
                ];
            });

            if ($request->ajax()) {
                return view('components.payout_request.order_table', ['entity' => $orders])->render();
            }

            return view('Admin::payout_requests.show', compact('data', 'orders', 'statuses', 'beneficiaries'));
        } catch (Throwable $th) {
            return redirect()->back()->with('error', 'Something went wrong. Please try again.' . $th->getMessage());
        }
    }

    // New method for updating beneficiary (handle the PATCH request from the form)
    public function updateBeneficiary(Request $request, $id): \Illuminate\Http\RedirectResponse
    {
        try {
            $request->validate([
                'beneficiary_id' => 'required|exists:payout_beneficiaries,id',
            ]);

            $data = $this->payoutRequestService->getPayoutRequestById($id);
            $data->update([
                'payout_beneficiary_id' => $request->beneficiary_id,
                'payout_method_changed_by' => auth()->id()  // Track who changed it (assumes 'changed_by' column exists as user_id foreign key)
            ]);

            return redirect()->back()->with('success', 'Payment method updated successfully.');
        } catch (Throwable $th) {
            return redirect()->back()->with('error', 'Failed to update payment method. Please try again.', );
        }
    }

    public function statusUpdate(Request $request, $request_id)
    {
        try {
            $payout = Payout::where('request_id', $request_id)->first() ?? Payout::findOrFail($request_id);
            $this->payoutRequestService->updateStatus($request, $payout->id);

            return redirect()->route('admin.payout-requests.index')->with('success', 'Payout request status updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function todaysPayout(Request $request)
    {
        $data = $this->payoutRequestService->getPayoutRequestsPaid($request);

        $filters = [
            'all' => [
                'label' => 'All',
                'icon' => 'fas fa-layer-group',
                'color' => 'primary'
            ],
            'bank' => [
                'label' => 'Bank Transfer',
                'icon' => 'fas fa-university',
                'color' => 'info'
            ],
            'mobile_all' => [
                'label' => 'Mobile Banking',
                'icon' => 'fas fa-mobile-alt',
                'color' => 'warning',
                'badge' => 'All'
            ],
            'bkash' => [
                'label' => 'bKash',
                'icon' => 'fas fa-money-bill-wave',
                'color' => 'danger',
                'badge' => 'Popular'
            ],
            'nagad' => [
                'label' => 'Nagad',
                'icon' => 'fas fa-wallet',
                'color' => 'success'
            ],
            'rocket' => [
                'label' => 'Rocket',
                'icon' => 'fas fa-bolt',
                'color' => 'primary'
            ],
        ];

        $statuses = PayoutRequestStatus::cases();

        if ($request->ajax()) {
            return view('components.payout_request.todays_payout_table', ['entity' => $data['payoutRequests']])->render();
        }
        return customView(['ajax' => 'components.payout_request.todays_payout_table', 'default' => 'Admin::payout_requests.todays_payout'], ['entity' => $data['payoutRequests'], 'filters' => $filters, 'statuses' => $statuses, 'totalAmount' => $data['totalAmount'], 'totalCount' => $data['totalCount']]);
    }


    public function getPaidBySfc()
    {
        // Static mode: SFC API integration is disabled
        return back()->with('info', 'SFC integration is disabled in static mode.');
    }

    public function paidBySfc(Request $request)
    {
        $perPage = $request->input('perPage', 10);
        $page = $request->input('page', 1);
        $paidBySfc = PaidBySfc::orderBy('created_at', 'desc')->paginate($perPage, ['*'], 'page', $page);
        return customView(['ajax' => 'components.payout_request.paid_by_sfc_table', 'default' => 'Admin::payout_requests.paid_by_sfc'], ['entity' => $paidBySfc]);
    }

    public function paidBySfcDetails($paymentId)
    {
        // Static mode: SFC API integration is disabled
        return back()->with('info', 'SFC integration is disabled in static mode.');
    }

    public function invoice($id)
    {
        $shopSettings = ShopSetting::whereIn('key', ['site_logo', 'contact_address', 'support_number'])->pluck('value', 'key')->toArray();
        $payout = $this->payoutRequestService->getPayoutRequestById($id);
        $orders = $payout->payoutMerchantOrders()->orderBy('created_at', 'asc')->get();
        return view('Admin::payout_requests.invoice', compact('payout', 'shopSettings', 'orders'));
    }


    public function report(Request $request)
    {
        $from_date = $request->input('from_date', now()->subDays(7)->format('Y-m-d'));
        $to_date = $request->input('to_date', now()->format('Y-m-d'));

        $methods = [
            'bkash' => [
                'label' => 'Bkash',
                'icon' => '<i class="fas fa-mobile-alt text-success me-2"></i>',
                'color' => 'text-success'
            ],
            'nagad' => [
                'label' => 'Nagad',
                'icon' => '<i class="fas fa-mobile-alt text-warning me-2"></i>',
                'color' => 'text-warning'
            ],
            'rocket' => [
                'label' => 'Rocket',
                'icon' => '<i class="fas fa-mobile-alt text-info me-2"></i>',
                'color' => 'text-info'
            ],
            'bank' => [
                'label' => 'Bank',
                'icon' => '<i class="fas fa-university text-primary me-2"></i>',
                'color' => 'text-primary'
            ],
        ];

        $summaries = [];
        foreach ($methods as $key => $info) {
            $temp_request = $request->duplicate();
            $temp_request->merge([
                'from_date' => $from_date,
                'to_date' => $to_date,
            ]);
            $summary = $this->payoutRequestService->getPayoutSummary($temp_request, $key);
            $summaries[$key] = array_merge($info, $summary);
        }

        $filters = [
            'bank' => ['label' => 'Bank'],
            'mobile_all' => ['label' => 'Bkash/Nagad/Rocket'],
            'bkash' => ['label' => 'Bkash'],
            'nagad' => ['label' => 'Nagad'],
            'rocket' => ['label' => 'Rocket'],
        ];

        return view('Admin::payout_requests.report.index', compact('summaries', 'from_date', 'to_date', 'filters'));
    }

    public function reportDetails(Request $request, $method)
    {
        $from_date = $request->input('from_date', now()->subDays(7)->format('Y-m-d'));
        $to_date = $request->input('to_date', now()->format('Y-m-d'));

        // Get total count and amount for the method
        $summary = $this->payoutRequestService->getPayoutSummary($request, $method);

        // Fetch detailed paginated list
        $temp_request = $request->duplicate();
        $temp_request->merge([
            'from_date' => $from_date,
            'to_date' => $to_date,
            'perPage' => 10,
            'page' => $request->input('page', 1),
        ]);
        $payouts = $this->payoutRequestService->getPayoutRequestsForMethod($temp_request, $method);

        // Dynamic method label
        $method_labels = [
            'bkash' => 'Bkash Payments',
            'nagad' => 'Nagad Payments',
            'rocket' => 'Rocket Payments',
            'bank' => 'Bank Payments (All Active Banks)',
        ];
        $method_label = $method_labels[$method] ?? ucfirst($method) . ' Payments';

        // Method info for icons/colors
        $method_info = [
            'bkash' => ['icon' => 'fas fa-mobile-alt text-success', 'color' => 'text-success'],
            'nagad' => ['icon' => 'fas fa-mobile-alt text-warning', 'color' => 'text-warning'],
            'rocket' => ['icon' => 'fas fa-mobile-alt text-info', 'color' => 'text-info'],
            'bank' => ['icon' => 'fas fa-university text-primary', 'color' => 'text-primary'],
        ];

        // Status options for badges (based on payouts.status)
        $status_options = [
            'pending' => 'bg-warning text-dark',
            'ready' => 'bg-info',
            'held' => 'bg-secondary',
            'paid' => 'bg-success',
            'rejected' => 'bg-danger',
            // Extend as per actual status values
        ];

        return view('Admin::payout_requests.report.details', compact(
            'method',
            'from_date',
            'to_date',
            'summary',
            'payouts',
            'method_label',
            'method_info',
            'status_options'
        ));
    }

    public function export(Request $request, $method = null)
    {
        $mfsMethods = ['bkash', 'nagad', 'rocket', 'mobile_all'];
        $currentFilter = $request->input('filter');

        if ($method === null) {
            // If it's an MFS filter, use the new MFSPayoutExport format
            if (in_array($currentFilter, $mfsMethods)) {
                $date = $request->input('date');

                return Excel::download(
                    new MFSPayoutExport($currentFilter, $date),
                    "{$currentFilter}_payout_export_" . ($date ?: now()->format('Y-m-d')) . ".xlsx"
                );
            }

            $exportRequest = $request->duplicate();
            $exportRequest->merge([
                'perPage' => 100000,
                'page' => 1,
            ]);

            $payouts = $this->payoutRequestService->getPayoutRequests($exportRequest);

            $date = $request->input('date');
            $fileNameDate = $date ?: now()->format('Y-m-d');
            $fileName = "payout_requests_{$fileNameDate}.xlsx";

            return Excel::download(
                new ExportPayoutRequests($payouts->getCollection()),
                $fileName
            );
        }

        $from_date = $request->input('from_date', now()->subDays(7)->format('Y-m-d'));
        $to_date = $request->input('to_date', now()->format('Y-m-d'));

        if ($from_date > $to_date) {
            return back()->with('error', 'From date must be before or equal to To date.');
        }

        if (in_array($method, $mfsMethods)) {
            return Excel::download(
                new MFSPayoutExport($method, $from_date, $to_date),
                "{$method}_payout_export_{$from_date}_to_{$to_date}.xlsx"
            );
        }

        return Excel::download(
            new PayoutReportExport($method, $from_date, $to_date),
            "{$method}_payout_report_{$from_date}_to_{$to_date}.xlsx"
        );
    }

    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'status' => 'required',
            'new_status' => 'required|in:ready,paid',
        ]);

        $statusMap = [
            'ready' => PayoutRequestStatus::READY->value,
            'paid' => PayoutRequestStatus::APPROVED->value,
        ];

        $newStatus = $statusMap[$request->new_status] ?? (int) $request->status;

        // Process all payouts matching the current filters, not just the current page.
        $payouts = $this->payoutRequestService->getFilteredPayoutRequestIds(
            $request,
            (int) $request->status
        );

        $request->merge(['status' => $newStatus]);

        foreach ($payouts as $payoutid) {
            $this->payoutRequestService->updateStatus($request, $payoutid);
        }

        return back()->with('success', 'Status updated successfully.');
    }
}
