<?php

namespace Modules\Api\V1\Merchant\Report\Http\Controllers;

use App\Enums\AccountTypes;
use App\Http\Controllers\Controller;
use App\Models\Account\Account;
use App\Models\Merchant\MerchantTransaction;
use App\Services\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('shop.permission:view-reports')->only('index');
    }

    public function index(Request $request): JsonResponse
    {
        if ($request->has('type')) {
            switch ($request->type) {
                case 'profit_loss':
                    return $this->profitLoss($request);
                case 'purchase_sale':
                    return $this->purchaseSale($request);
                case 'account_transactions':
                    return $this->accountTransactions($request);
                case 'balance_sheet':
                    return $this->balanceSheet($request);
                default:
                    break;
            }
        }

        return ApiResponse::failure('Type parameter is required', Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    private function profitLoss($request): JsonResponse
    {
        $startDate  = $request->start_date   ?? date('Y-m-d');
        $endDate    = $request->end_date     ?? date('Y-m-d');
        $merchantId = $request->user()->merchant->id;

        // Retrieve all accounts with transactions filtered by date range
        $hasDateRange = $request->filled('start_date') && $request->filled('end_date')
            && strtolower((string) $request->start_date) !== 'all'
            && strtolower((string) $request->end_date) !== 'all';

        $transactionFilter = function ($query) use ($startDate, $endDate, $hasDateRange) {
            if ($hasDateRange) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }
        };
        $accountTypes = Account::where('merchant_id', $merchantId)
            ->whereIn('account_type', [AccountTypes::INCOME->value, AccountTypes::PURCHASE->value, AccountTypes::EXPENSE->value, AccountTypes::SALE->value])
            ->with([
                'merchantTransactions' => $transactionFilter
            ])
            ->withCount(['merchantTransactions as transactions_sum' => $transactionFilter])
            ->get()
            ->groupBy('account_type');

        // Calculate adjusted balances
        function adjustedBalance($accounts, $type)
        {
            return (float) MerchantTransaction::whereIn('account_id', $accounts[$type]->pluck('id'))->sum('amount');
        }

        // Get income, purchase, and expense balances
        $totalIncome   = adjustedBalance($accountTypes, AccountTypes::INCOME->value);
        $totalPurchase = adjustedBalance($accountTypes, AccountTypes::PURCHASE->value);

        $totalExpense =
            (float) $accountTypes[AccountTypes::EXPENSE->value]->sum(function ($account) {
                return $account->balance - $account->merchantTransactions->sum('amount');
            }) ?? 0;

        // Process sales data
        $salesData = [];
        if (isset($accountTypes[AccountTypes::SALE->value])) {
            foreach ($accountTypes[AccountTypes::SALE->value] as $account) {
                $salesData[$account->slug] = (float) ($account->balance - $account->merchantTransactions->sum('amount'));
            }
        }

        // Calculate profit
        $profit = $totalIncome - ($totalExpense + $totalPurchase);

        $totalTransactions = $accountTypes->flatten()->sum('transactions_sum');
        // Merge the sales data with other financial data
        $responseData = array_merge($salesData, [
            'total_income'     => $totalIncome,
            'product_purchase' => $totalPurchase,
            'total_expense'    => $totalExpense,
            'profit'           => $profit,
            'total_transaction_found'   => $totalTransactions,
        ]);

        return ApiResponse::success('success', $responseData, Response::HTTP_OK);
    }

    private function purchaseSale($request): JsonResponse
    {
        $startDate  = $request->start_date   ?? date('Y-m-d 00:00:00');
        $endDate    = $request->end_date     ?? date('Y-m-d 23:59:59');
        $merchantId = $request->user()->merchant->id;

        $hasDateRange = $request->filled('start_date') && $request->filled('end_date')
            && strtolower((string) $request->start_date) !== 'all'
            && strtolower((string) $request->end_date) !== 'all';

        $transactionFilter = function ($query) use ($startDate, $endDate, $hasDateRange) {
            if ($hasDateRange) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }
        };

        // Retrieve relevant accounts with transactions filtered by date range
        $accounts = Account::where('merchant_id', $merchantId)
            ->whereIn('account_type', [AccountTypes::PURCHASE->value, AccountTypes::SUPPLIER->value, AccountTypes::SALE->value])
            ->with([
                'merchantTransactions' => $transactionFilter,
            ])
            ->withCount(['merchantTransactions as transactions_sum' => $transactionFilter])
            ->get()
            ->keyBy('account_type_id');

        // Helper function to get adjusted balance
        function adjustedBalance($accounts, $type)
        {
            $balance           = (float) ($accounts[$type]->balance ?? 0);
            $transactionsTotal = (float) ($accounts[$type]->merchantTransactions->sum('amount') ?? 0);

            return $balance - $transactionsTotal;
        }

        // Get purchase and sale balances
        $totalPurchase    = adjustedBalance($accounts, AccountTypes::PURCHASE->value);
        $totalPurchaseDue = adjustedBalance($accounts, AccountTypes::SUPPLIER->value);
        $totalSale        = adjustedBalance($accounts, AccountTypes::SALE->value);

        // Sale and purchase return logic (assumed 0 for now, update if needed)
        $totalPurchaseReturn = 0;
        $totalSaleDue        = 0;
        $totalSaleReturn     = 0;

        $totalTransactions = $accounts->sum('transactions_sum');

        return ApiResponse::success('success', [
            'total_sell'              => $totalSale,
            'total_sell_return'       => $totalSaleReturn,
            'total_sell_due'          => $totalSaleDue,
            'total_purchase'          => $totalPurchase,
            'total_purchase_return'   => $totalPurchaseReturn,
            'total_purchase_due'      => $totalPurchaseDue,
            'sale_minus_purchase'     => $totalSale - $totalPurchase,
            'total_due'               => $totalPurchaseDue,
            'total_transaction' => $totalTransactions,
        ], Response::HTTP_OK);
    }

    private function accountTransactions($request): JsonResponse
    {
        $startDate = $request->start_date;
        $endDate   = $request->end_date;
        $hasDateRange = $request->filled('start_date') && $request->filled('end_date')
            && strtolower((string) $startDate) !== 'all'
            && strtolower((string) $endDate)   !== 'all';

        $merchantTransactions = $request->user()->merchant->merchantTransactions()
            ->when($hasDateRange, function ($q) use ($startDate, $endDate) {
                $q->whereBetween('date', [$startDate, $endDate]);
            })
            ->get();

        $transactions = $merchantTransactions->map(function ($transaction) {
            return [
                'date'        => $transaction->created_at->format('Y-m-d'),
                'account'     => $transaction->account->name                           ?? 'N/A',
                'description' => $transaction->note                                    ?? 'N/A',
                'type'        => $transaction->type                                    ?? 'N/A',
                'reason'      => $transaction->reason                                  ?? 'N/A',
                'reference'   => $transaction->reference                               ?? 'N/A',
                'debit'       => $transaction->type == 'debit' ? $transaction->amount  ?? 0 : 0,
                'credit'      => $transaction->type == 'credit' ? $transaction->amount ?? 0 : 0,
                'amount'      => $transaction->amount                                  ?? 0,
            ];
        });

        $totalDebit  = $merchantTransactions->where('type', 'debit')->sum('amount');
        $totalCredit = $merchantTransactions->where('type', 'credit')->sum('amount');

        return ApiResponse::success('success', [
            'transactions' => $transactions,
            'summary'      => [
                'total_debit'     => (float) $totalDebit,
                'total_credit'    => (float) $totalCredit,
                'closing_balance' => (float) $totalCredit - $totalDebit,
            ],
        ], Response::HTTP_OK);
    }

    private function balanceSheet($request): JsonResponse
    {
        $startDate  = $request->start_date   ?? date('Y-m-d');
        $endDate    = $request->end_date     ?? date('Y-m-d');
        $merchantId = $request->user()->merchant->id;
        // Fetch all relevant accounts grouped by type with transactions
        $accountTypes = Account::where('merchant_id', $merchantId)
            ->whereIn('account_type', [AccountTypes::CASH->value, AccountTypes::BANK->value, AccountTypes::ASSET->value, AccountTypes::LIABILITIES->value, AccountTypes::EQUITY->value])
            ->with([
                'merchantTransactions' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                },
            ])
            ->get()
            ->groupBy('account_type_id');

        $response                      = [];
        $grandTotalAssets              = 0;
        $grandTotalLiabilitiesEquities = 0;

        // Helper to calculate balance per account
        foreach ($accountTypes as $accounts) {
            $accountsData = [];
            $total        = 0;
            $type         = $accounts->first();

            foreach ($accounts as $account) {
                $transactionSum = $account->merchantTransactions->sum('amount');
                $accountBalance = $account->balance + $transactionSum;
                $accountsData[] = [
                    'account_name' => $account->name,
                    'balance'      => $accountBalance,
                ];
                $total += $accountBalance;
            }

            $response[] = [
                'type'     => $type->account_type_name,
                'accounts' => $accountsData,
                'total'    => $total,
            ];

            // Classify assets vs liabilities & equities
            if (in_array($type->account_type_id, [AccountTypes::CASH->value, AccountTypes::BANK->value, AccountTypes::ASSET->value])) {
                $grandTotalAssets += $total;
            } else {
                $grandTotalLiabilitiesEquities += $total;
            }
        }

        // Return grouped data with totals
        return ApiResponse::success('Balance sheet generated successfully', [
            'accounts' => $response,
            'totals'   => [
                'asset_total'                => $grandTotalAssets,
                'liabilities_equities_total' => $grandTotalLiabilitiesEquities,
            ],
        ], Response::HTTP_OK);
    }

    public function showBalanceSheet(Request $request): JsonResponse
    {
        $startDate  = $request->start_date   ?? date('Y-m-d');
        $endDate    = $request->end_date     ?? date('Y-m-d');
        $merchantId = $request->user()->merchant->id;
        // Fetch all relevant accounts grouped by type with transactions
        $accountIds = Account::where('merchant_id', $merchantId)
            ->when($request->type == 'sales', function ($query) {
                $query->where(['account_type' => AccountTypes::SALE->value]);
            })
            ->when($request->type == 'purchases', function ($query) {
                $query->where(['account_type' => AccountTypes::PURCHASE->value]);
            })
            ->when($request->type == 'expenses', function ($query) {
                $query->where(['account_type' => AccountTypes::EXPENSE->value]);
            })
            ->pluck('id');

        $hasDateRange = $request->filled('start_date') && $request->filled('end_date')
            && strtolower((string) $request->start_date) !== 'all'
            && strtolower((string) $request->end_date) !== 'all';

        $transactionsQuery = MerchantTransaction::query()
            ->whereIn('account_id', $accountIds)
            ->when($hasDateRange, function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->get()
            ->map(function ($transaction) {
                return [
                    'date'    => $transaction->created_at->format('Y-m-d'),
                    'account' => $transaction->account->name ?? 'N/A',
                    'amount'  => $transaction->amount,
                    'type'    => $transaction->type,
                ];
            });

        return ApiResponse::success('Balance sheet generated successfully', [
            'transactions' => $transactionsQuery,
            'summary'      => [
                'total_debit'     => $transactionsQuery->where('type', 'debit')->sum('amount'),
                'total_credit'    => $transactionsQuery->where('type', 'credit')->sum('amount'),
                'closing_balance' => $transactionsQuery->where('type', 'debit')->sum('amount') - $transactionsQuery->where('type', 'credit')->sum('amount'),
            ],
        ], Response::HTTP_OK);
    }
}
