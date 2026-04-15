<?php

namespace Modules\Api\V1\Ecommerce\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Api\V1\Ecommerce\Auth\Http\Requests\ChangePasswordRequest;
use Modules\Api\V1\Ecommerce\Auth\Http\Requests\LoginRequest;
use Modules\Api\V1\Ecommerce\Auth\Http\Requests\NewPasswordRequest;
use Modules\Api\V1\Ecommerce\Auth\Http\Requests\RegisterRequest;
use Modules\Api\V1\Ecommerce\Auth\Http\Requests\ResetConfirmRequest;
use Modules\Api\V1\Ecommerce\Auth\Http\Requests\ResetPasswordRequest;
use Modules\Api\V1\Ecommerce\Auth\Http\Requests\SendOtpRequest;
use Modules\Api\V1\Ecommerce\Auth\Http\Requests\UpdateUserRequest;
use Modules\Api\V1\Ecommerce\Auth\Http\Requests\VerifyOtpEmailRequest;
use Modules\Api\V1\Ecommerce\Auth\Http\Requests\VerifyOtpRequest;
use App\Models\User\Otp;
use App\Models\User\SocketToken;
use App\Models\User\User;
use App\Services\SmsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class AuthController extends Controller
{
    /**
     * @throws Throwable
     */
    public function sendOtp(SendOtpRequest $request)
    {
        $request->validated();
        $mobileNumber = $request->phone;

        // Check if the phone number is already registered
        $user = User::where('phone', $mobileNumber)->where('role', 3)->first();

        if ($user?->status === '0') {
            return failure('Your account is deactivated. Please contact support.');
        }

        if ($user && $user->password) {
            return success('User already registered. Please login instead.', ['password' => true]);
        }

        // Check if OTP has expired or not after 2 minutes
        $otp = Otp::where('phone', $mobileNumber)->where('is_verified', 0)->first();
        if ($otp && ! Carbon::now()->greaterThan($otp->expires_at)) {
            return success('Please wait for some time before requesting a new OTP.', null);
        }

        // Start transaction
        DB::beginTransaction();

        try {
            // Generate OTP
            $code = rand(100000, 999999);
            // Set OTP expiration (2 minutes from now)
            $expiresAt = Carbon::now()->addMinutes(2);

            // Create or update OTP record
            $otp = Otp::updateOrCreate(
                ['phone' => $mobileNumber],
                [
                    'otp'         => $code,
                    'expires_at'  => $expiresAt,
                    'is_verified' => 0,
                ]
            );

            // Send OTP to user
            $smsService  = new SmsService;
            $smsResponse = $smsService->sendMessage(
                $mobileNumber,
                "Welcome to Packly.com! Use OTP $code to complete your registration. OGYBjP6uXzA"
            );

            // Log SMS response
            //  Log::info('SMS Response: ', $smsResponse);

            // Commit the transaction
            DB::commit();

            return success('OTP sent successfully.', null);
        } catch (\Exception $e) {
            // If any exception occurs, rollback the transaction
            DB::rollBack();

            // Log the exception
            Log::error('Error sending OTP: '.$e->getMessage());


            return failure('Something went wrong', 500);
        }
    }

    /**
     * @throws Throwable
     */
    public function sendOtpToEmail()
    {
        $user = auth()->user();

        if (! $user) {
            return failure('User not found.');
        }

        if (! $user->email) {
            return failure('Please set your email first.');
        }

        if ($user?->status === '0') {
            return failure('Your account is deactivated. Please contact support.');
        }

        if ($user?->email_verified_at) {
            return success('Your email is already verified.');
        }

        // Check if OTP has expired or not after 2 minutes
        $otp = Otp::where('email', $user->email)->where('is_verified', 0)->first();
        if ($otp && ! Carbon::now()->greaterThan($otp->expires_at)) {
            return success('Please wait for some time before requesting a new OTP.', null);
        }

        // Start transaction
        DB::beginTransaction();

        try {
            // Generate OTP
            $code = rand(100000, 999999);
            // Set OTP expiration (2 minutes from now)
            $expiresAt = Carbon::now()->addMinutes(2);

            // Create or update OTP record
            $otp = Otp::updateOrCreate(
                ['email' => $user->email],
                [
                    'otp'         => $code,
                    'expires_at'  => $expiresAt,
                    'is_verified' => 0,
                ]
            );

            // Send OTP to user
            // Mail::to($user->email)->send(new VerificationMail($code));

            // Commit the transaction
            DB::commit();

            return success('OTP sent successfully.', null);
        } catch (\Exception $e) {
            // If any exception occurs, rollback the transaction
            DB::rollBack();

            // Log the exception
            Log::error('Error sending OTP: '.$e->getMessage());


            return failure('Something went wrong', 500);
        }
    }

    public function login(LoginRequest $request)
    {
        $request->validated();

        $mobileNumber = $request->phone;
        $password     = $request->password;

        $user = User::where('phone', $mobileNumber)->where('role', 3)->first();

        if (! $user || ! Hash::check($password, $user->password) || $user->status === '0') {
            return validationError('Validation Error', [
                'phone' => ['Invalid phone number or password.'],
            ]);
        }

        $tokenResult                                = $user->createToken('authToken');
        $token                                      = $tokenResult->plainTextToken;
        $socketToken                                = $this->generateSocketToken();
        $socketTokenModel                           = new SocketToken;
        $socketTokenModel->user_id                  = (int) $user->id;
        $socketTokenModel->personal_access_token_id = (int) $tokenResult->accessToken->id;
        $socketTokenModel->token                    = $socketToken;
        $socketTokenModel->save();
        DB::commit();

        return success('Logged in successfully.', ['user' => $user, 'token' => $token, 'socket_token' => $socketToken]);
    }

    /**
     * @throws Throwable
     */
    public function verifyOtp(VerifyOtpRequest $request)
    {
        $request->validated();

        $mobileNumber = $request->phone;
        $code         = $request->otp;

        $opt = Otp::where('phone', $mobileNumber)->where('otp', $code)->where('is_verified', 0)->first();
        if (! $opt) {
            return failure('Invalid OTP.');
        }

        // Check if OTP has expired or not after 2 minutes
        if (Carbon::now()->greaterThan($opt->expires_at)) {
            return failure('OTP has expired. Please try again.');
        }

        // Start transaction
        DB::beginTransaction();

        try {
            // Clear OTP fields
            $opt->expires_at  = null;
            $opt->is_verified = 1;
            $opt->save();

            // Find user by mobile number
            $user = User::where('phone', $mobileNumber)->where('role', User::$ROLE_USER)->first();

            $set_password = true;
            if (! $user) {
                $user = User::create([
                    'name'     => null,
                    'password' => null,
                    'phone'    => $mobileNumber,
                ]);
                $set_password = false;
            }

            if ($user) {
                $user->phone_verified_at = now();
                $user->save();

                $tokenResult                                = $user->createToken('authToken');
                $token                                      = $tokenResult->plainTextToken;
                $socketToken                                = $this->generateSocketToken();
                $socketTokenModel                           = new SocketToken;
                $socketTokenModel->user_id                  = (int) $user->id;
                $socketTokenModel->personal_access_token_id = (int) $tokenResult->accessToken->id;
                $socketTokenModel->token                    = $socketToken;
                $socketTokenModel->save();

                DB::commit();

                if (! $user->password) {
                    return success('User registered successfully.', ['user' => $user, 'set_password' => $set_password, 'token' => $token, 'socket_token' => $socketToken]);
                }

                return success('Logged in successfully.', ['user' => $user, 'token' => $token, 'socket_token' => $socketToken]);
            }

            // If user doesn't exist, handle account creation flow
            DB::commit();

            return success('Create account.', null);
        } catch (\Exception $e) {
            // If any exception occurs, rollback the transaction
            DB::rollBack();

            return failure('Something went wrong', 500);
        }
    }

    public function verifyOtpEmail(VerifyOtpEmailRequest $request)
    {
        $request->validated();

        $user  = auth()->user();
        $email = $user?->email;
        $code  = $request->otp;

        if (! $user->email) {
            return failure('Email not found.');
        }

        if ($user->email_verified_at) {
            return failure('Email already verified.');
        }

        $emailOtp = Otp::where('email', $email)
            ->where('otp', $code)
            ->where('is_verified', 0)
            ->first();

        if (! $emailOtp) {
            return failure('Invalid OTP.');
        }

        if (Carbon::now()->greaterThan($emailOtp->expires_at)) {
            return failure('OTP has expired. Please request a new one.');
        }

        try {
            DB::beginTransaction();
            // Mark OTP as used
            $emailOtp->is_verified = 1;
            $emailOtp->expires_at  = null;
            $emailOtp->save();

            $user->email_verified_at = now();
            $user->save();

            DB::commit();

            return success('Email verified successfully.', ['user' => $user]);
        } catch (\Exception $e) {
            DB::rollBack();

            return failure('Something went wrong: '.$e->getMessage());
        }
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $request->validated();

        try {
            DB::beginTransaction();

            $user = User::create([
                'name'     => $request->name,
                'phone'    => $request->phone,
                'password' => Hash::make($request->password),
            ]);

            $opt = Otp::where('phone', $request->phone)->where('otp', $request->otp)->first();

            if (! $opt) {
                return failure('Invalid OTP.');
            }

            $token       = $user->createToken('authToken');
            $tokenString = $token->plainTextToken;
            $socketToken = $this->generateSocketToken();

            $socketTokenModel                           = new SocketToken;
            $socketTokenModel->user_id                  = $user->id;
            $socketTokenModel->personal_access_token_id = $token->accessToken->id;
            $socketTokenModel->token                    = $socketToken;
            $socketTokenModel->save();

            $opt->otp = '';
            $opt->save();

            DB::commit();

            return success('User created successfully', ['user' => $user, 'token' => $tokenString, 'socket_token' => $socketToken]);
        } catch (\Exception $e) {
            DB::rollBack();

            return failure('Error creating user'.$e->getMessage());
        }
    }

    /**
     * @throws Throwable
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        $request->validated();

        $mobileNumber = $request->phone;

        // Check if OTP has expired or not after 2 minutes
        $otp = Otp::where('phone', $mobileNumber)->where('is_verified', 0)->first();
        if ($otp && ! Carbon::now()->greaterThan($otp->expires_at)) {
            return success('Please wait for some time before requesting a new OTP.', null);
        }

        // Start transaction
        DB::beginTransaction();

        try {
            // Generate OTP
            $code = rand(100000, 999999);
            // Set OTP expiration (2 minutes from now)
            $expiresAt = Carbon::now()->addMinutes(2);

            // Create or update OTP record
            $otp = Otp::updateOrCreate(
                ['phone' => $mobileNumber],
                [
                    'otp'         => $code,
                    'expires_at'  => $expiresAt,
                    'is_verified' => 0,
                ]
            );

            // Send OTP to user
            $smsService  = new SmsService;
            $smsResponse = $smsService->sendMessage(
                $mobileNumber,
                "Your OTP to reset password on Packly.com is $code. Thanks NELYNyb29FL"
            );

            // Log SMS response
            //  Log::info('SMS Response: ', $smsResponse);

            // Commit the transaction
            DB::commit();

            return success('OTP sent successfully.', null);
        } catch (\Exception $e) {
            // If any exception occurs, rollback the transaction
            DB::rollBack();

            // Log the exception
            Log::error('Error sending OTP: '.$e->getMessage());

            return failure('Something went wrong', 500);
        }
    }

    public function resetConfirm(ResetConfirmRequest $request): JsonResponse
    {
        $request->validated();

        try {
            DB::beginTransaction();

            $user = User::where('phone', $request->phone)->where('role', User::$ROLE_USER)->first();

            if (! $user) {
                return failure('User not found.');
            }

            $opt = Otp::where('phone', $request->phone)->where('otp', $request->otp)->where('is_verified', 1)->first();

            if (! $opt) {
                return failure('Invalid or expired OTP.');
            }

            $user->password = Hash::make($request->password);
            $user->save();

            // Revoke all existing tokens after password reset (no current token in this flow)
            $user->tokens()->delete();

            $token       = $user->createToken('authToken');
            $tokenString = $token->plainTextToken;
            $socketToken = $this->generateSocketToken();

            $socketTokenModel                           = new SocketToken;
            $socketTokenModel->user_id                  = $user->id;
            $socketTokenModel->personal_access_token_id = $token->accessToken->id;
            $socketTokenModel->token                    = $socketToken;
            $socketTokenModel->save();

            $opt->otp = '';
            $opt->save();

            DB::commit();

            return success('Password reset successfully', ['user' => $user, 'token' => $tokenString, 'socket_token' => $socketToken]);
        } catch (\Exception $e) {
            DB::rollBack();

            return failure('Error creating user'.$e->getMessage());
        }
    }

    public function getUserDetails(): JsonResponse
    {
        $user = auth()->user();
        if (! $user) {
            return failure('User not found');
        }

        return success('User details fetched successfully', $user);
    }

    public function updateUser(UpdateUserRequest $request): JsonResponse
    {
        $user = auth()->user();

        if (! $user) {
            return failure('User not found.');
        }

        $request->validated();

        if ($user->hasVerifiedEmail() && $user->email != $request->email) {
            return failure('You cannot change your verified email.');
        }

        try {
            DB::beginTransaction();

            $user->update($request->only(['name', 'email', 'date_of_birth', 'gender', 'avatar']));

            DB::commit();

            return success('User updated successfully', ['user' => $user, 'token' => $request->bearerToken()]);
        } catch (\Exception $e) {
            DB::rollBack();

            return failure('Error updating user: '.$e->getMessage());
        }
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $request->validated();

        $user = auth()->user();

        $user->password = Hash::make($request->password);
        $user->save();

        // Revoke all other tokens, keep the current token (if any)
        $currentToken = $user->currentAccessToken();
        if ($currentToken) {
            $user->tokens()->where('id', '!=', $currentToken->id)->delete();
        } else {
            $user->tokens()->delete();
        }

        return success('Password changed successfully.');
    }

    public function newPassword(NewPasswordRequest $request): JsonResponse
    {
        $request->validated();
        $user = auth()->user();

        if (! $user) {
            return failure('User not found.');
        }
        $user->password = Hash::make($request->password);
        $user->save();

        // Revoke all other tokens, keep the current token (if any)
        $currentToken = $user->currentAccessToken();
        if ($currentToken) {
            $user->tokens()->where('id', '!=', $currentToken->id)->delete();
        } else {
            $user->tokens()->delete();
        }

        $tokenResult                                = $user->createToken('authToken');
        $token                                      = $tokenResult->plainTextToken;
        $socketToken                                = $this->generateSocketToken();
        $socketTokenModel                           = new SocketToken;
        $socketTokenModel->user_id                  = (int) $user->id;
        $socketTokenModel->personal_access_token_id = (int) $tokenResult->accessToken->id;
        $socketTokenModel->token                    = $socketToken;
        $socketTokenModel->save();

        return success('Password updated successfully.', ['user' => $user, 'token' => $token, 'socket_token' => $socketToken]);
    }

    public function logout(): JsonResponse
    {
        $expiredTokens = auth()->user()->tokens()->where('expires_at', '<', now())->get();

        foreach ($expiredTokens as $token) {
            SocketToken::where('personal_access_token_id', $token->id)->delete();
            $token->delete();
        }

        // delete current token
        $currentToken = auth()->user()->currentAccessToken();

        SocketToken::where('personal_access_token_id', $currentToken->id)->delete();

        auth()->user()->currentAccessToken()->delete();

        return success('Logout successful');
    }

    private function generateSocketToken()
    {
        $uniqueId = Str::random(32);

        $socketTokens = SocketToken::where('token', $uniqueId)->first();

        if ($socketTokens) {
            return $this->generateSocketToken();
        }

        return $uniqueId;
    }
}
