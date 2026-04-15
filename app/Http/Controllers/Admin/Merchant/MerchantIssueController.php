<?php

namespace App\Http\Controllers\Admin\Merchant;

use App\Enums\MerchantIssueStatus;
use App\Http\Controllers\Controller;
use App\Models\Merchant\MerchantIssue;
use Illuminate\Http\Request;
use Throwable;

class MerchantIssueController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:merchant-issue-list')->only('index');
        $this->middleware('permission:merchant-issue-update')->only('update');
    }

    /**
     * List merchant issues with filters similar to Shop Update Requests.
     *
     * @throws Throwable
     */
    public function index(Request $request)
    {
        $search      = $request->input('search');
        $searchType  = $request->input('search_type');
        $statusLabel = $request->input('status'); // pending|in-progress|resolved
        $perPage     = $request->input('perPage') ?? 10;

        $statusMap = [
            'pending'     => MerchantIssueStatus::Pending->value,
            'in-progress' => MerchantIssueStatus::InProgress->value,
            'resolved'    => MerchantIssueStatus::Resolved->value,
        ];

        $issues = MerchantIssue::with(['merchant.userRelation', 'type', 'media'])
            ->when(isset($statusMap[$statusLabel]), function ($q) use ($statusMap, $statusLabel) {
                $q->where('status', $statusMap[$statusLabel]);
            })
            ->when($search, function ($q) use ($search, $searchType) {
                if ($searchType === 'id') {
                    $q->where('merchant_id', (int) $search);
                } else {
                    $q->where(function ($qq) use ($search) {
                        $qq->where('message', 'like', "%{$search}%")
                            ->orWhereHas('type', function ($tq) use ($search) {
                                $tq->where('name', 'like', "%{$search}%");
                            });
                    });
                }
            })
            ->latest('created_at')
            ->paginate($perPage)
            ->withQueryString();

        if ($request->ajax()) {
            return view('components.merchant-issues.table', ['issues' => $issues])->render();
        }

        return view('backend.pages.merchant_issues.index', compact('issues'));
    }

    public function update(Request $request, MerchantIssue $merchantIssue)
    {
        $request->validate([
            'status' => 'required|in:pending,in-progress,resolved',
        ]);

        $statusMap = [
            'pending'     => MerchantIssueStatus::Pending->value,
            'in-progress' => MerchantIssueStatus::InProgress->value,
            'resolved'    => MerchantIssueStatus::Resolved->value,
        ];

        $merchantIssue->update([
            'status' => $statusMap[$request->input('status')],
        ]);

        return redirect()->back()->with('success', 'Merchant issue status updated successfully.');
    }
}
