<?php

namespace Modules\Api\V1\Merchant\Account\Http\Controllers;

use App\Enums\AccountTypes;
use App\Http\Controllers\Controller;
use App\Models\Account\Account;
use App\Services\ApiResponse;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AccountChartController extends Controller
{
    public function __construct()
    {
        $this->middleware('shop.permission:show-account-chart')->only('chartAccountsByAccountType', 'chartAccountsByExpenseAccountType');
    }
    public function chartAccountsByAccountType(Request $request, int $id): JsonResponse
    {
        try {
            $perPage = $request->query('per_page', 10);

            $accountCharts = Account::where('merchant_id', Auth::user()->merchant->id)
                ->where('account_type', $id)
                ->orderBy('id', 'desc')
                ->paginate($perPage);

            return ApiResponse::formatPagination('Chart of accounts for account type', $accountCharts);
        } catch (ModelNotFoundException) {
            return ApiResponse::failure('Account type not found.', Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return ApiResponse::failure('Account type not found.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function chartAccountsByExpenseAccountType(): JsonResponse
    {
        try {
            $expenses = Account::where('merchant_id', Auth::user()->merchant->id)
                ->where('account_type', AccountTypes::EXPENSE->value)
                ->orderBy('id', 'desc')
                ->get();

            return ApiResponse::success('All Expense accounts', $expenses, Response::HTTP_OK);
        } catch (ModelNotFoundException) {
            return ApiResponse::failure('Account type not found.', Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return ApiResponse::failure('Account type not found.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
