<?php

namespace App\Services;

use App\Enums\AccountTypes;
use App\Enums\OrderStatus;
use App\Enums\PayoutRequestStatus;
use App\Models\Account\Account;
use App\Models\Merchant\MerchantTransaction;
use App\Models\Payment\Payout;
use App\Models\Payment\PayoutBeneficiaryBank;
use App\Models\Payment\PayoutBeneficiaryMobileWallet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PayoutRequestService
{
    private function buildPayoutRequestBaseQuery($request)
    {
        $search = $request->input('search', '');
        $date = $request->input('date', '');
        $merchant_id = $request->input('merchant_id', '');
        $payout_beneficiary_type_id = $request->input('payout_beneficiary_type_id', '');
        $payout_beneficiary_mobile_wallet_id = $request->input('payout_beneficiary_mobile_wallet_id', '');
        $payout_beneficiary_bank_id = $request->input('payout_beneficiary_bank_id', '');

        $filter = $request->input('filter', '');

        // Get active IDs for group filters
        $active_bank_ids = PayoutBeneficiaryBank::where('status', 1)->pluck('id')->toArray();
        $active_mobile_ids = PayoutBeneficiaryMobileWallet::where('status', 1)->pluck('id')->toArray();

        $mfsMethods = ['bkash', 'nagad', 'rocket'];

        if (in_array($filter, $mfsMethods)) {
            $wallet = PayoutBeneficiaryMobileWallet::where('status', 1)
                ->whereRaw('LOWER(name) = ?', [strtolower($filter)])
                ->first();
            $payout_beneficiary_mobile_wallet_id = $wallet ? [$wallet->id] : [];
            $payout_beneficiary_type_id = 1; // Mobile
        } else {
            // Override beneficiary-related filters if a group filter is applied
            if ($filter !== '') {
                $payout_beneficiary_type_id = '';
                $payout_beneficiary_mobile_wallet_id = '';
                $payout_beneficiary_bank_id = '';

                switch ($filter) {
                    case 'all':
                        // No override needed for all
                        break;
                    case 'bank':
                        $payout_beneficiary_bank_id = $active_bank_ids;
                        break;
                    case 'mobile_all':
                        $payout_beneficiary_mobile_wallet_id = $active_mobile_ids;
                        break;
                }
            }
        }

        return Payout::query()
            ->when($merchant_id, function ($query) use ($merchant_id) {
                return $query->where('merchant_id', $merchant_id);
            })
            ->when($date, function ($query) use ($date) {
                $query->whereDate('created_at', $date);
            })
            ->when($payout_beneficiary_type_id, function ($query) use ($payout_beneficiary_type_id) {
                $query->whereHas('payoutBeneficiary', function ($q) use ($payout_beneficiary_type_id) {
                    $q->where('payout_beneficiary_type_id', $payout_beneficiary_type_id);
                });
            })
            ->when($payout_beneficiary_mobile_wallet_id, function ($query) use ($payout_beneficiary_mobile_wallet_id) {
                $query->whereHas('payoutBeneficiary', function ($q) use ($payout_beneficiary_mobile_wallet_id) {
                    if (is_array($payout_beneficiary_mobile_wallet_id) && !empty($payout_beneficiary_mobile_wallet_id)) {
                        $q->whereIn('payout_beneficiary_mobile_wallet_id', $payout_beneficiary_mobile_wallet_id);
                    } elseif (!empty($payout_beneficiary_mobile_wallet_id)) {
                        $q->where('payout_beneficiary_mobile_wallet_id', $payout_beneficiary_mobile_wallet_id);
                    }
                });
            })
            ->when($payout_beneficiary_bank_id, function ($query) use ($payout_beneficiary_bank_id) {
                $query->whereHas('payoutBeneficiary', function ($q) use ($payout_beneficiary_bank_id) {
                    if (is_array($payout_beneficiary_bank_id) && !empty($payout_beneficiary_bank_id)) {
                        $q->whereIn('payout_beneficiary_bank_id', $payout_beneficiary_bank_id);
                    } elseif (!empty($payout_beneficiary_bank_id)) {
                        $q->where('payout_beneficiary_bank_id', $payout_beneficiary_bank_id);
                    } else {
                        $q->whereNotNull('payout_beneficiary_bank_id');
                    }
                });
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('request_id', 'like', "%{$search}%")
                        ->orWhereHas('merchant', function ($merchantQuery) use ($search) {
                            $merchantQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('phone', 'like', "%{$search}%")
                                ->orWhere('shop_name', 'like', "%{$search}%");
                        });
                });
            });
    }

    public function getPayoutRequests($request)
    {
        $perPage = $request->input('perPage', 10);
        $page = $request->input('page', 1);
        $status = (int) $request->input('status', ''); // Allow null from frontend
        $query = $this->buildPayoutRequestBaseQuery($request)
            ->with(['merchant', 'payoutBeneficiary'])
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->orderBy('created_at', 'desc');

        return $query
            ->paginate($perPage, ['*'], 'page', $page);
    }

    public function getPayoutBalanceSummary($request, int $status): array
    {
        $query = $this->buildPayoutRequestBaseQuery($request)
            ->where('status', $status);

        $totalAmount = (clone $query)->toBase()->sum('amount');
        $totalCount = (clone $query)->toBase()->count();

        return [
            'totalAmount' => (float) $totalAmount,
            'totalCount' => (int) $totalCount,
        ];
    }

    public function getFilteredPayoutRequestIds($request, int $status, array $requestIds = []): array
    {
        $query = $this->buildPayoutRequestBaseQuery($request)
            ->where('status', $status);

        if (!empty($requestIds)) {
            $query->whereIn('id', $requestIds);
        }

        return $query->pluck('id')->toArray();
    }

    public function getPayoutRequestsPaid($request): array
    {
        $perPage = $request->input('perPage', 10);
        $page = $request->input('page', 1);
        $search = $request->input('search', '');
        $date = $request->input('date', '');
        $merchant_id = $request->input('merchant_id', '');
        $payout_beneficiary_type_id = $request->input('payout_beneficiary_type_id', '');
        $payout_beneficiary_mobile_wallet_id = $request->input('payout_beneficiary_mobile_wallet_id', '');
        $payout_beneficiary_bank_id = $request->input('payout_beneficiary_bank_id', '');

        $filter = $request->input('filter', '');

        // Get active IDs for group filters
        $active_bank_ids = PayoutBeneficiaryBank::where('status', 1)->pluck('id')->toArray();
        $active_mobile_ids = PayoutBeneficiaryMobileWallet::where('status', 1)->pluck('id')->toArray();

        // Override beneficiary-related filters if a group filter is applied
        if ($filter !== '') {
            $payout_beneficiary_type_id = '';
            $payout_beneficiary_mobile_wallet_id = '';
            $payout_beneficiary_bank_id = '';

            switch ($filter) {
                case 'all':
                    // No override needed for all
                    break;
                case 'bank':
                    $payout_beneficiary_bank_id = $active_bank_ids;
                    break;
                case 'mobile_all':
                    $payout_beneficiary_mobile_wallet_id = $active_mobile_ids;
                    break;
                case 'bkash':
                    $payout_beneficiary_mobile_wallet_id = [1]; // Use array for consistency
                    break;
                case 'nagad':
                    $payout_beneficiary_mobile_wallet_id = [2];
                    break;
                case 'rocket':
                    $payout_beneficiary_mobile_wallet_id = [3];
                    break;
            }
        }

        $query = Payout::query()
            ->with(['merchant', 'payoutBeneficiary'])
            ->when($merchant_id, function ($query) use ($merchant_id) {
                return $query->where('merchant_id', $merchant_id);
            })
            ->when($date, function ($query) use ($date) {
                $query->whereDate('paid_at', $date);
            })
            ->when($payout_beneficiary_type_id, function ($query) use ($payout_beneficiary_type_id) {
                $query->whereHas('payoutBeneficiary', function ($q) use ($payout_beneficiary_type_id) {
                    $q->where('payout_beneficiary_type_id', $payout_beneficiary_type_id);
                });
            })
            ->when($payout_beneficiary_mobile_wallet_id, function ($query) use ($payout_beneficiary_mobile_wallet_id) {
                $query->whereHas('payoutBeneficiary', function ($q) use ($payout_beneficiary_mobile_wallet_id) {
                    if (is_array($payout_beneficiary_mobile_wallet_id) && !empty($payout_beneficiary_mobile_wallet_id)) {
                        $q->whereIn('payout_beneficiary_mobile_wallet_id', $payout_beneficiary_mobile_wallet_id);
                    } elseif (!empty($payout_beneficiary_mobile_wallet_id)) {
                        $q->where('payout_beneficiary_mobile_wallet_id', $payout_beneficiary_mobile_wallet_id);
                    }
                });
            })
            ->when($payout_beneficiary_bank_id, function ($query) use ($payout_beneficiary_bank_id) {
                $query->whereHas('payoutBeneficiary', function ($q) use ($payout_beneficiary_bank_id) {
                    if (is_array($payout_beneficiary_bank_id) && !empty($payout_beneficiary_bank_id)) {
                        $q->whereIn('payout_beneficiary_bank_id', $payout_beneficiary_bank_id);
                    } elseif (!empty($payout_beneficiary_bank_id)) {
                        $q->where('payout_beneficiary_bank_id', $payout_beneficiary_bank_id);
                    } else {
                        $q->whereNotNull('payout_beneficiary_bank_id');
                    }
                });
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('request_id', 'like', "%{$search}%")
                        ->orWhereHas('merchant', function ($merchantQuery) use ($search) {
                            $merchantQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('phone', 'like', "%{$search}%")
                                ->orWhere('shop_name', 'like', "%{$search}%");
                        });
                });
            });

        $totalAmount = (clone $query)->toBase()->sum('amount');

        $payoutRequests = $query
            ->orderBy('paid_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        return [
            'payoutRequests' => $payoutRequests,
            'totalAmount' => $totalAmount,
            'totalCount' => $payoutRequests->total(),
        ];
    }


    public function getPayoutRequestById($id)
    {
        return Payout::with(['merchant', 'payoutBeneficiary', 'payoutMerchantOrders', 'paidBy', 'payoutMethodChangedBy'])->find($id);
    }

    public function updateStatus($request, $id): void
    {
        $payoutRequest = Payout::with('payoutMerchantOrders')->findOrFail($id);

        $newStatus = $request->status;

        // Handle approved status
        if ($payoutRequest->status != PayoutRequestStatus::APPROVED && $newStatus == PayoutRequestStatus::APPROVED->value) {

            $uuid = Str::uuid();

            $this->updateAccountBalance(
                $payoutRequest->merchant_id,
                $payoutRequest->amount,
                AccountTypes::ASSET->value,
                'PYRC',
                'debit',
                'decrement',
                $uuid
            );

            $payoutRequest->paid_at = now();
            $payoutRequest->paid_by = Auth::user()->id;
            $payoutRequest->merchant->increment('balance', $payoutRequest->amount);

            foreach ($payoutRequest->payoutMerchantOrders as $payoutMerchantOrder) {
                $payoutMerchantOrder->update([
                    'payout_id' => $payoutRequest->id
                ]);
            }
        }

        // Set timestamps based on new status
        $statusTimestamps = [
            PayoutRequestStatus::READY->value => ['ready_at', 'ready_by'],
            PayoutRequestStatus::HOLDED->value => ['held_at', 'held_by'],
        ];

        if (isset($statusTimestamps[$newStatus])) {
            [$timestampField, $userField] = $statusTimestamps[$newStatus];

            $payoutRequest->{$timestampField} = now();
            $payoutRequest->{$userField} = Auth::id(); // or Auth::user()->id
        }


        // Update status and save
        $payoutRequest->status = $newStatus;
        $payoutRequest->save();
    }


    public function updateAccountBalance($merchantId, $amount, $accountType, $uucode = null, $type = 'credit', $method = 'increment', $uuid = null, $accountId = null)
    {
        if ($accountId) {
            $account = Account::where('merchant_id', $merchantId)
                ->where('id', $accountId)
                ->first();
        } elseif ($uucode == 'PCAH') {
            $account = Account::where('merchant_id', $merchantId)
                ->where('uucode', $uucode)
                ->first();
        } else {
            $account = Account::where('merchant_id', $merchantId)
                ->where('account_type', $accountType)
                ->when($uucode, function ($query, $uucode) {
                    $query->where('uucode', $uucode);
                })
                ->first();
        }

        $account->{$method}('balance', $amount);

        MerchantTransaction::create([
            'uuid' => $uuid,
            'merchant_id' => $merchantId,
            'account_id' => $account->id,
            'amount' => $method === 'increment' ? $amount : -$amount,
            'date' => now(),
            'type' => $type,
        ]);
    }

    public function getPayoutSummary($request, $filter = '')
    {
        // Get active IDs for group filters
        $active_bank_ids = PayoutBeneficiaryBank::where('status', 1)->pluck('id')->toArray();

        // Override beneficiary-related filters if a group filter is applied
        if ($filter !== '') {
            $request->merge([
                'payout_beneficiary_type_id' => '',
                'payout_beneficiary_mobile_wallet_id' => '',
                'payout_beneficiary_bank_id' => '',
            ]);

            switch ($filter) {
                case 'bank':
                    $request->merge(['payout_beneficiary_bank_id' => $active_bank_ids]);
                    break;
                case 'bkash':
                    $bkash_wallet = PayoutBeneficiaryMobileWallet::where('status', 1)->where('name', 'bkash')->first();
                    $request->merge(['payout_beneficiary_mobile_wallet_id' => $bkash_wallet ? [$bkash_wallet->id] : []]);
                    break;
                case 'nagad':
                    $nagad_wallet = PayoutBeneficiaryMobileWallet::where('status', 1)->where('name', 'Nagad')->first();
                    $request->merge(['payout_beneficiary_mobile_wallet_id' => $nagad_wallet ? [$nagad_wallet->id] : []]);
                    break;
                case 'rocket':
                    $rocket_wallet = PayoutBeneficiaryMobileWallet::where('status', 1)->where('name', 'Rocket')->first();
                    $request->merge(['payout_beneficiary_mobile_wallet_id' => $rocket_wallet ? [$rocket_wallet->id] : []]);
                    break;
                case 'mobile_all':
                    $active_mobile_ids = PayoutBeneficiaryMobileWallet::where('status', 1)->pluck('id')->toArray();
                    $request->merge(['payout_beneficiary_mobile_wallet_id' => $active_mobile_ids]);
                    break;
            }
        }

        $query = Payout::query()->where('status', PayoutRequestStatus::APPROVED->value)
            ->selectRaw('COUNT(*) as count, COALESCE(SUM(amount), 0) as total_amount')
            ->when($request->input('from_date'), function ($query) use ($request) {
                $query->whereDate('created_at', '>=', $request->input('from_date'));
            })
            ->when($request->input('to_date'), function ($query) use ($request) {
                $query->whereDate('created_at', '<=', $request->input('to_date'));
            })
            ->when($request->input('payout_beneficiary_type_id'), function ($query) use ($request) {
                $payout_beneficiary_type_id = $request->input('payout_beneficiary_type_id');
                $query->whereHas('payoutBeneficiary', function ($q) use ($payout_beneficiary_type_id) {
                    $q->where('payout_beneficiary_type_id', $payout_beneficiary_type_id);
                });
            })
            ->when($request->input('payout_beneficiary_mobile_wallet_id'), function ($query) use ($request) {
                $payout_beneficiary_mobile_wallet_id = $request->input('payout_beneficiary_mobile_wallet_id');
                $query->whereHas('payoutBeneficiary', function ($q) use ($payout_beneficiary_mobile_wallet_id) {
                    if (is_array($payout_beneficiary_mobile_wallet_id) && !empty($payout_beneficiary_mobile_wallet_id)) {
                        $q->whereIn('payout_beneficiary_mobile_wallet_id', $payout_beneficiary_mobile_wallet_id);
                    } elseif (!empty($payout_beneficiary_mobile_wallet_id)) {
                        $q->where('payout_beneficiary_mobile_wallet_id', $payout_beneficiary_mobile_wallet_id);
                    }
                });
            })
            ->when($request->input('payout_beneficiary_bank_id'), function ($query) use ($request) {
                $payout_beneficiary_bank_id = $request->input('payout_beneficiary_bank_id');
                $query->whereHas('payoutBeneficiary', function ($q) use ($payout_beneficiary_bank_id) {
                    if (is_array($payout_beneficiary_bank_id) && !empty($payout_beneficiary_bank_id)) {
                        $q->whereIn('payout_beneficiary_bank_id', $payout_beneficiary_bank_id);
                    } elseif (!empty($payout_beneficiary_bank_id)) {
                        $q->where('payout_beneficiary_bank_id', $payout_beneficiary_bank_id);
                    } else {
                        $q->whereNotNull('payout_beneficiary_bank_id');
                    }
                });
            });

        $result = $query->first();

        return [
            'count' => (int) ($result->count ?? 0),
            'total' => (float) ($result->total_amount ?? 0),
        ];
    }


    public function getPayoutRequestsForMethod($request, $method)
    {
        $perPage = $request->input('perPage', 10);
        $page = $request->input('page', 1);

        // Get active IDs for group filters
        $active_bank_ids = PayoutBeneficiaryBank::where('status', 1)->pluck('id')->toArray();
        $active_mobile_ids = PayoutBeneficiaryMobileWallet::where('status', 1)->pluck('id')->toArray();

        // Apply method-specific filters
        $payout_beneficiary_type_id = '';
        $payout_beneficiary_mobile_wallet_id = '';
        $payout_beneficiary_bank_id = '';

        $mfsMethods = ['bkash', 'nagad', 'rocket'];

        if (in_array($method, $mfsMethods)) {
            $wallet = PayoutBeneficiaryMobileWallet::where('status', 1)
                ->whereRaw('LOWER(name) = ?', [strtolower($method)])
                ->first();
            $payout_beneficiary_mobile_wallet_id = $wallet ? [$wallet->id] : [];
            $payout_beneficiary_type_id = 1; // Mobile
        } else {
            switch ($method) {
                case 'bank':
                    $payout_beneficiary_bank_id = $active_bank_ids;
                    $payout_beneficiary_type_id = 2; // Bank
                    break;
                case 'mobile_all':
                    $payout_beneficiary_mobile_wallet_id = $active_mobile_ids;
                    $payout_beneficiary_type_id = 1; // Mobile
                    break;
                default:
                    return collect(); // No data for invalid method
            }
        }

        return Payout::query() // Model/table: payouts
            ->with([
                'merchant:id,name,phone', // From merchants table
                'payoutBeneficiary:id,merchant_id,payout_beneficiary_type_id,payout_beneficiary_mobile_wallet_id,payout_beneficiary_bank_id,account_holder_name,account_number,branch_name,routing_number',
                'payoutBeneficiary.bank:id,name', // Corrected: Uses 'bank' relationship
                'payoutBeneficiary.mobileWallet:id,name' // Corrected: Uses 'mobileWallet' relationship
            ])
            ->when($request->input('from_date'), function ($query) use ($request) {
                $query->whereDate('created_at', '>=', $request->input('from_date'));
            })
            ->when($request->input('to_date'), function ($query) use ($request) {
                $query->whereDate('created_at', '<=', $request->input('to_date'));
            })
            ->when($payout_beneficiary_type_id, function ($query) use ($payout_beneficiary_type_id) {
                $query->whereHas('payoutBeneficiary', function ($q) use ($payout_beneficiary_type_id) {
                    $q->where('payout_beneficiary_type_id', $payout_beneficiary_type_id);
                });
            })
            ->when($payout_beneficiary_mobile_wallet_id, function ($query) use ($payout_beneficiary_mobile_wallet_id) {
                $query->whereHas('payoutBeneficiary', function ($q) use ($payout_beneficiary_mobile_wallet_id) {
                    if (is_array($payout_beneficiary_mobile_wallet_id) && !empty($payout_beneficiary_mobile_wallet_id)) {
                        $q->whereIn('payout_beneficiary_mobile_wallet_id', $payout_beneficiary_mobile_wallet_id);
                    }
                });
            })
            ->when($payout_beneficiary_bank_id, function ($query) use ($payout_beneficiary_bank_id) {
                $query->whereHas('payoutBeneficiary', function ($q) use ($payout_beneficiary_bank_id) {
                    if (is_array($payout_beneficiary_bank_id) && !empty($payout_beneficiary_bank_id)) {
                        $q->whereIn('payout_beneficiary_bank_id', $payout_beneficiary_bank_id);
                    }
                });
            })
            ->select(
                'id',
                'request_id',
                'merchant_id',
                'amount',
                'status',
                'created_at',
                'payout_beneficiary_id'
            )
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);
    }
}
