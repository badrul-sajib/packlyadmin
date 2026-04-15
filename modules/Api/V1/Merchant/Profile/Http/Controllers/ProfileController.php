<?php

namespace Modules\Api\V1\Merchant\Profile\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Api\V1\Merchant\Profile\Http\Requests\ProfileRequest;
use App\Services\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ProfileController extends Controller
{
    public function show(Request $request)
    {

        $user = $request->user();

        return ApiResponse::success('User info', [
            'name'  => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'image' => $user->image,
        ]);
    }

    public function update(ProfileRequest $request)
    {
        try {
            $request->validated();

            DB::beginTransaction();
            $user        = $request->user();
            $user->name  = $request->name;
            $user->image = $request->image;
            $user->save();

            $user->merchant->name = $request->name;
            $user->merchant->save();
            DB::commit();

            return ApiResponse::success('Profile updated successfully', $user->image, Response::HTTP_OK);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Profile Update Error: ' . $th->getMessage(), ['stack' => $th->getTraceAsString()]);

            return ApiResponse::failure('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function passwordReset(ProfileRequest $request)
    {
        $request->validated();

        $user = $request->user();
        if (! Hash::check($request->old_password, $user->password)) {
            return ApiResponse::failure('The provided password was incorrect.', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if (Hash::check($request->password, $user->password)) {
            return ApiResponse::failure('New password cannot be same as old password.', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user->update(['password' => Hash::make($request->password)]);

        // Revoke all other tokens, keep the current token (if any)
        $currentToken = $user->currentAccessToken();
        if ($currentToken) {
            $user->tokens()->where('id', '!=', $currentToken->id)->delete();
        } else {
            $user->tokens()->delete();
        }

        return ApiResponse::success('Password updated successfully', Response::HTTP_OK);
    }
}
