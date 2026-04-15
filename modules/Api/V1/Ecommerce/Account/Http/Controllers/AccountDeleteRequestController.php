<?php

namespace Modules\Api\V1\Ecommerce\Account\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Api\V1\Ecommerce\Account\Http\Requests\AccountDeleteFormRequest;
use App\Models\Account\AccountDeleteRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class AccountDeleteRequestController extends Controller
{
    public function store(AccountDeleteFormRequest $request)
    {
        $user = Auth::user();

        $validated = $request->validated();

        try {
            // Check if user already has a pending request
            $existingRequest = AccountDeleteRequest::where('user_id', $user->id)->first();

            if ($existingRequest) {
                return response()->json([
                    'message' => 'You already have an account deletion request',
                ], 409);
            }

            DB::beginTransaction();
            // Create new deletion request
            $deleteRequest = AccountDeleteRequest::create([
                'user_id' => $user->id,
                'reason'  => $validated['reason'] ?? '',
                'status'  => AccountDeleteRequest::STATUS_APPROVED,
            ]);

            $user->update(['status' => '0']);

            DB::commit();

            return success('Account deletion request submitted successfully', $deleteRequest, Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            DB::rollBack();

            return failure('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function checkPendingRequest()
    {
        $user = Auth::user();

        $pendingRequest = AccountDeleteRequest::where('user_id', $user->id)->first();

        return response()->json([
            'has_pending_request' => $pendingRequest !== null,
            'request'             => $pendingRequest,
        ]);
    }
}
