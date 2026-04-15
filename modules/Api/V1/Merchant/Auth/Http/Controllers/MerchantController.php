<?php

namespace Modules\Api\V1\Merchant\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Merchant\Api\V1\Auth\AuthController;
use Modules\Api\V1\Merchant\Auth\Http\Requests\MerchantRegisterRequest;
use App\Models\Shop\ShopSetting;
use App\Models\User\Otp;
use App\Services\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class MerchantController extends Controller
{
    /*
     * Registers a new merchant account.
     */
    public function store(MerchantRegisterRequest $request): JsonResponse
    {
        $emailOtpWaitingTime = 03;
        $phoneOtpWaitingTime = 03;

        try {
            $data = $request->validated();

            $waitingTime = $data['otp_type'] === 'email' ? $emailOtpWaitingTime : $phoneOtpWaitingTime;

            $otpEntry = Otp::where($data['otp_type'], $data[$data['otp_type']])
                ->where('created_at', '>=', Carbon::now()->subMinutes($waitingTime))
                ->first();

            if ($otpEntry) {
                return ApiResponse::validationError("OTP already sent. Please wait $waitingTime minutes before requesting a new one.", [], Response::HTTP_TOO_MANY_REQUESTS);
            }

            // Proceed with sending OTP if not already sent
            $otpResponse = app(AuthController::class)->registerSendOtp($request, $waitingTime);

            if ($otpResponse->getStatusCode() !== Response::HTTP_OK) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Failed to send OTP. Please try again later.',
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            // Store form data temporarily in cache
            Cache::put('pending_registration_'.$data[$data['otp_type']], $data, Carbon::now()->addMinutes($waitingTime));

            return ApiResponse::success('OTP sent successfully. Please verify to complete registration.',
                [
                    'waiting_time' => $waitingTime,
                ], Response::HTTP_OK);
        } catch (ValidationException $e) {
            return ApiResponse::validationError('There were validation errors.', $e->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
