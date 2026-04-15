<?php

namespace Modules\Api\V1\Merchant\Account\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Api\V1\Merchant\Account\Http\Requests\JournalRequest;
use App\Models\Account\Journal;
use App\Models\Account\JournalDetail;
use App\Services\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class JournalController extends Controller
{

    public function __construct()
    {
        $this->middleware('shop.permission:show-journal')->only('index');
        $this->middleware('shop.permission:create-journal')->only('store');
    }

    // Fetch all journals
    public function index(): JsonResponse
    {
        $journals = Journal::with('details')->get();

        return ApiResponse::success('Journals fetched successfully.', $journals, Response::HTTP_OK);
    }

    // Store a new journal with its details

    /**
     * @throws Throwable
     */
    public function store(JournalRequest $request): JsonResponse
    {
        $validated = $request->validated();

        DB::beginTransaction();

        try {
            // Create the journal
            $journal = Journal::create([
                'date'        => $validated['date'],
                'journal_no'  => $validated['journal_no'],
                'referrence'  => $validated['referrence'],
                'note'        => $validated['note'],
                'merchant_id' => $validated['merchant_id'],
            ]);

            // Create the journal details
            foreach ($validated['details'] as $detail) {
                JournalDetail::create([
                    'journal_id'  => $journal->id,
                    'type'        => $detail['type'],
                    'account_id'  => $detail['account_id'],
                    'description' => $detail['description'],
                    'contact_id'  => $detail['contact_id'],
                    'amount'      => $detail['amount'],
                ]);
            }

            DB::commit();

            return ApiResponse::success('Journal created successfully.', [], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollBack();

            return ApiResponse::failure('Failed to create journal', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
