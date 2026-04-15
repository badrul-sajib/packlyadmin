<?php

namespace Modules\Api\V1\Merchant\Auth\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use Modules\Api\V1\Merchant\Auth\Http\Requests\LoginRequest;
use Modules\Api\V1\Merchant\Auth\Http\Requests\OtpRegisterVerifyRequest;
use Modules\Api\V1\Merchant\Auth\Http\Requests\OtpValidationRequest;
use Modules\Api\V1\Merchant\Auth\Http\Requests\PhoneValidationRequest;
use Modules\Api\V1\Merchant\Auth\Http\Requests\RegisterWithKeyRequest;
use Modules\Api\V1\Merchant\Auth\Http\Requests\ResetPasswordRequest;
use App\Mail\OtpMail;
use App\Models\Merchant\Merchant;
use App\Models\Setting\ShopSetting;
use App\Models\User\Otp;
use App\Models\User\PersonalAccessToken;
use App\Models\User\SocketToken;
use App\Models\User\User;
use App\Services\ApiResponse;
use App\Services\SmsService;
use App\Services\UserDefaultDataService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Spatie\Permission\Models\Permission;
use Throwable;

class AuthController extends Controller
{
    protected UserDefaultDataService $userDefaultDataService;

    public function __construct(UserDefaultDataService $userDefaultDataService)
    {
        $this->userDefaultDataService = $userDefaultDataService;
    }


    /**
     * Log in a user via token or phone/password.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $request->validated();

            $user = null;

            if ($request->has('token')) {
                $user = $this->validateToken($request);
                if (! $user) {
                    return ApiResponse::failure('Invalid token', Response::HTTP_UNAUTHORIZED);
                }
            } else {
                $user = User::whereIn('role', [UserRole::MERCHANT->value, UserRole::SHOP_ADMIN->value])
                    ->where(function ($query) use ($request) {
                        $query->where('phone', $request->phone)
                            ->orWhere('email', $request->phone);
                    })
                    ->first();
                if (! $user || ! Hash::check($request->password, $user->password)) {
                    return ApiResponse::failure('Username or password incorrect', Response::HTTP_UNPROCESSABLE_ENTITY);
                }
            }

            if (!in_array($user->role->value, [UserRole::MERCHANT->value, UserRole::SHOP_ADMIN->value])) {
                return ApiResponse::failure('Access denied for this user role', Response::HTTP_FORBIDDEN);
            }

            if (
                $user->role->value == UserRole::SHOP_ADMIN->value &&
                $user->status == 0
            ) {
                return ApiResponse::failure(
                    'Your account is inactive. Please contact your administrator.',
                    Response::HTTP_FORBIDDEN
                );
            }
            if (
                $user->role->value == UserRole::MERCHANT->value &&
                $user->status == 0
            ) {
                return ApiResponse::failure(
                    'Your account is inactive. Please contact your administrator.',
                    Response::HTTP_FORBIDDEN
                );
            }
            $tokenResult       = $user->createToken('auth_token');
            $token             = $tokenResult->accessToken;
            $expiresAt         = now()->addDays(1);
            $token->expires_at = $expiresAt;
            $token->save();

            $keyMap = [
                'delivery_charge'  => 'ed_delivery_fee',
                'shipping_fee_isd' => 'id_delivery_fee',
                'shipping_fee_osd' => 'od_delivery_fee',
            ];

            $settings = ShopSetting::whereIn('key', array_keys($keyMap))->pluck('value', 'key');

            $shippingData = [];

            foreach ($keyMap as $dbKey => $label) {
                $shippingData[$label] = $settings[$dbKey] ?? 0;
            }

            $info = ShopSetting::whereIn('key', ['contact_number', 'contact_email'])->pluck('value', 'key');

            $currentHost = request()->getHost();

            if (filter_var($currentHost, FILTER_VALIDATE_IP)) {
                $rootDomain = $currentHost;
            } else {
                $hostParts  = explode('.', $currentHost);
                $rootDomain = implode('.', array_slice($hostParts, -2));
            }

            $domain = request()->getScheme() . '://' . $rootDomain;

            return ApiResponse::success('User logged in successfully', [
                'name' => $user->name,
                'user' => [
                    'id'          => $user->id,
                    'name'        => $user->name,
                    'role_type'   => $user->role->value == UserRole::MERCHANT->value ? $user->role->label() : $this->getUserRole($user),
                    'permissions' => $this->getUserPermissions($user),
                    'shop_name'   => $user->merchant ? $user->merchant->shop_name : '',
                    'merchant_id' => $user->merchant ? $user->merchant->id : '',
                    'phone'       => $user->phone,
                    'address'     => $user->merchant->shop_address ?? '',
                ],
                'token'         => $tokenResult->plainTextToken,
                'image'         => $user->image ?? '',
                'delivery_fees' => $shippingData,
                'expires_at'    => $expiresAt->toDateTimeString(),
                'info'          => [
                    'phone'          => $info['contact_number'] ?? '',
                    'email'          => $info['contact_email']  ?? '',
                    'packly_website' => $domain,
                ],
            ], Response::HTTP_OK);
        } catch (ValidationException $e) {
            return ApiResponse::validationError(
                'Validation failed',
                $e->errors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return ApiResponse::failure('An unexpected error occurred. Please try again later.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            $expiredTokens = $request->user()->tokens()->where('expires_at', '<', now())->get();

            foreach ($expiredTokens as $token) {
                SocketToken::where('personal_access_token_id', $token->id)->delete();
                $token->delete();
            }
            // Delete current token
            $currentToken = $request->user()->currentAccessToken();

            $personalAccessToken = PersonalAccessToken::find($currentToken->id);

            SocketToken::where('personal_access_token_id', $currentToken->id)->delete();

            if ($personalAccessToken) {
                $personalAccessToken->delete();
            }

            return ApiResponse::success('User logged out successfully', null, Response::HTTP_OK);
        } catch (\Exception $e) {
            return ApiResponse::failure('An unexpected error occurred. Please try again later.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /*
     * Sends an OTP to a user’s phone.
     */
    /**
     * @throws Throwable
     */
    public function sendOtp(PhoneValidationRequest $request): JsonResponse
    {
        $mobileNumber = $request->phone;

        // Start transaction
        DB::beginTransaction();

        try {

            $otp = Otp::where('phone', $mobileNumber)->where('is_verified', 0)->first();

            if ($otp && ! Carbon::now()->greaterThan($otp->expires_at)) {
                return success('Please wait for some time before requesting a new OTP.', null);
            }

            // Generate OTP
            $code = random_int(100000, 999999);
            // Set OTP expiration (2 minutes from now)
            $expiresAt = Carbon::now()->addMinutes(2);

            // Create or update OTP record
            Otp::updateOrCreate(
                ['phone' => $mobileNumber],
                [
                    'otp'         => $code,
                    'expires_at'  => $expiresAt,
                    'is_verified' => 0,
                ]
            );

            // Send OTP to user
            $smsService = new SmsService;
            $smsService->sendMessage(
                $mobileNumber,
                "Packly - Your OTP is $code. It will expire in 2 minutes. Thanks NELYNyb29FL"
            );

            // Commit the transaction
            DB::commit();

            return ApiResponse::success('OTP sent successfully.', null, Response::HTTP_OK);
        } catch (\Exception $e) {
            // If any exception occurs, rollback the transaction
            DB::rollBack();

            // Log the exception
            Log::error('Error sending OTP: ' . $e->getMessage());

            return ApiResponse::failure('An unexpected error occurred. Please try again later.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Sent otp to register user
     *
     * @throws Throwable
     */
    public function registerSendOtp(Request $request, $waitingTime): JsonResponse
    {
        $otpType = $request->otp_type == 'email' ? 'email' : 'phone';

        $destination = $otpType === 'email' ? $request->email : $request->phone;

        // Check if OTP has expired or not after 2 minutes
        $otp = Otp::where($otpType, $destination)
            ->where('is_verified', 0)
            ->first();

        if ($otp && ! Carbon::now()->greaterThan($otp->expires_at)) {
            return ApiResponse::failure('Please wait for some time before requesting a new OTP.');
        }

        // Start transaction
        DB::beginTransaction();

        try {
            // Generate OTP
            $code = random_int(100000, 999999);
            // Set OTP expiration
            $expiresAt = Carbon::now()->addMinutes($waitingTime);

            // Create or update OTP record
            $otpData = [
                'otp'         => $code,
                'expires_at'  => $expiresAt,
                'is_verified' => 0,
            ];

            if ($otpType === 'email') {
                $otpData['email'] = $destination;
            } else {
                $otpData['phone'] = $destination;
            }

            Otp::updateOrCreate(
                [$otpType => $destination],
                $otpData
            );

            if ($otpType === 'email') {
                // Send OTP via email
                Mail::to($destination)->queue(new OtpMail($code));
            } else {
                // Send OTP via SMS
                $smsService = new SmsService;
                $smsService->sendMessage(
                    $destination,
                    "Welcome to Packly.com! Use OTP $code to complete your registration. Thanks NELYNyb29FL"
                );
            }

            // Commit the transaction
            DB::commit();

            return ApiResponse::success('OTP sent successful.', null, Response::HTTP_OK);
        } catch (\Exception $e) {
            // Rollback the transaction
            DB::rollBack();

            // Log the exception
            Log::error('Error sending OTP: ' . $e->getMessage());

            return ApiResponse::failure('Failed to send OTP. Please try again.', Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Verifies OTP via phone or email.
     *
     * @throws Throwable
     */
    public function verifyOtp(OtpValidationRequest $request): JsonResponse
    {
        $request->validated();

        $mobileNumber = $request->phone;
        $code         = $request->otp;

        $opt = Otp::where('phone', $mobileNumber)->whereNot('is_verified', 1)->where('otp', $code)->first();

        if (! $opt) {
            return ApiResponse::failure('Invalid OTP.', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Check if OTP has expired or not after 2 minutes
        if (Carbon::now()->greaterThan($opt->expires_at)) {
            return ApiResponse::failure('OTP has expired. Please try again.', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Start transaction
        DB::beginTransaction();

        try {

            // if request has new_registration flag, then create a new user
            if ($request->has('new_registration') && $request->new_registration) {

                // Clear OTP fields
                $opt->otp         = '';
                $opt->expires_at  = null;
                $opt->is_verified = 1;
                $opt->save();

                $key = Str::random(16);
                // Cache OTP verified for that phone number
                $cacheKey = 'pending_registration_' . $mobileNumber . '_' . $key;

                Cache::put($cacheKey, $mobileNumber, now()->addMinutes(30));

                DB::commit();

                return ApiResponse::success('OTP verified successfully.', ['phone' => $mobileNumber, 'reg_key' => $cacheKey], Response::HTTP_OK);
            }

            // Find user by mobile number
            $user = User::where('phone', $mobileNumber)->whereIn('role', [UserRole::MERCHANT->value, UserRole::SHOP_ADMIN->value])->first();

            if ($user) {
                DB::commit();
                return ApiResponse::success('OTP verified successfully.', ['phone' => $mobileNumber], Response::HTTP_OK);
            }

            // If user doesn't exist, handle account creation flow
            DB::commit();

            return ApiResponse::success('Account created successfully.', null, Response::HTTP_OK);
        } catch (\Exception $e) {
            // If any exception occurs, rollback the transaction
            DB::rollBack();

            return ApiResponse::failure('Failed to verify OTP.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * User register with a key
     *
     * @throws Throwable
     */
    public function registerWithKey(RegisterWithKeyRequest $request): JsonResponse
    {
        $request->validated();
        // Retrieve registration data from cache
        $cacheKey = $request->reg_key;

        $checkCache = Cache::get($cacheKey);

        if (! $checkCache) {
            return ApiResponse::failure('Otp has expired. Please try again.');
        }

        $registrationData = $request->only([
            'name',
            'phone',
            'email',
            'password',
            'shop_address',
            'shop_name',
            'shop_url',
        ]);

        // get phone number from cache
        $mobileNumber = Cache::get($cacheKey);

        if ($mobileNumber !== $registrationData['phone']) {
            return ApiResponse::failure('Phone number does not match the registration key.');
        }

        // Start transaction
        DB::beginTransaction();
        DB::connection('mysql_external')->beginTransaction();

        try {
            // Create User
            $user = User::create([
                'name'     => $registrationData['name'],
                'phone'    => $registrationData['phone'] ?? null,
                'email'    => $registrationData['email'] ?? null,
                'password' => Hash::make($registrationData['password']),
                'role'     => UserRole::MERCHANT->value,
            ]);

            // Create Merchant
            $merchant = Merchant::create([
                'uuid'              => Merchant::generateUniqueUuid(),
                'name'              => $registrationData['name'],
                'phone'             => $registrationData['phone'] ?? null,
                'shop_address'      => $registrationData['shop_address'],
                'shop_name'         => $registrationData['shop_name'],
                'slug'              => $registrationData['shop_name'] ? Str::slug($registrationData['shop_name']) : null,
                'shop_url'          => $registrationData['shop_url'],
                'shop_status'       => 0,
                'is_popular_enable' => 0, // default merchant is not popular
            ]);

            DB::table('shop_users')->insert([
                'user_id' => $user->id,
                'shop_id' => $merchant->id,
            ]);

            // Call the service to insert default data
            $this->userDefaultDataService->insert($merchant);

            // Generate authentication token (same as login)
            $tokenResult       = $user->createToken('auth_token');
            $token             = $tokenResult->accessToken;
            $expiresAt         = now()->addDays(1);
            $token->expires_at = $expiresAt;
            $token->save();

            // Fetch delivery fees (same as login)
            $keyMap = [
                'shipping_fee_exd' => 'ed_delivery_fee',
                'shipping_fee_isd' => 'id_delivery_fee',
                'shipping_fee_osd' => 'od_delivery_fee',
            ];

            $settings     = ShopSetting::whereIn('key', array_keys($keyMap))->pluck('value', 'key');
            $shippingData = [];

            foreach ($keyMap as $dbKey => $label) {
                $shippingData[$label] = $settings[$dbKey] ?? 0;
            }

            // Remove cached data
            Cache::forget($cacheKey);

            DB::commit();
            DB::connection('mysql_external')->commit();

            $info = ShopSetting::whereIn('key', ['contact_number', 'contact_email'])->pluck('value', 'key');

            $currentHost = request()->getHost();

            if (filter_var($currentHost, FILTER_VALIDATE_IP)) {
                $rootDomain = $currentHost;
            } else {
                $hostParts  = explode('.', $currentHost);
                $rootDomain = implode('.', array_slice($hostParts, -2));
            }

            $domain = request()->getScheme() . '://' . $rootDomain;

            // Return response identical to login
            return ApiResponse::success('Merchant Registration and Login Successful', [
                'name' => $user->name,
                'user' => [
                    'id'          => $user->id,
                    'name'        => $user->name,
                    'role_type'   => $user->role->label(),
                    'permissions' => $this->getUserPermissions($user),
                    'shop_name'   => $user->merchant ? $user->merchant->shop_name : '',
                    'merchant_id' => $user->merchant ? $user->merchant->id : '',
                    'phone'       => $user->phone,
                    'address'     => $user->merchant->shop_address ?? '',
                ],
                'token'         => $tokenResult->plainTextToken,
                'image'         => $user->image ?? '',
                'delivery_fees' => $shippingData,
                'expires_at'    => $expiresAt->toDateTimeString(),
                'info'          => [
                    'phone'          => $info['contact_number'] ?? '',
                    'email'          => $info['contact_email']  ?? '',
                    'packly_website' => $domain,
                ],
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollBack();
            DB::connection('mysql_external')->rollBack();
            Log::error('Error in registerVerifyOtp: ' . $e->getMessage());

            return ApiResponse::failure('Registration and login failed', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Verifies OTP and completes user registration
     *
     * @throws Throwable
     */
    public function registerVerifyOtp(OtpRegisterVerifyRequest $request): JsonResponse
    {
        $request->validated();

        $identifierType = $request->otp_type;
        $identifier     = $request->otp_type === 'email' ? $request->email : $request->phone;
        $code           = $request->otp;

        $otp = Otp::where($identifierType, $identifier)
            ->where('otp', $code)
            ->first();

        if (! $otp) {
            return ApiResponse::failure('Invalid OTP.');
        }

        // Check if OTP is expired
        if (Carbon::now()->greaterThan($otp->expires_at)) {
            return ApiResponse::failure('OTP has expired. Please try again.');
        }

        // Retrieve cached registration data
        $cacheKey         = 'pending_registration_' . $identifier;
        $registrationData = Cache::get($cacheKey);

        if (! $registrationData) {
            return ApiResponse::failure('No pending registration found. Please register again.');
        }

        DB::beginTransaction();
        DB::connection('mysql_external')->beginTransaction();

        try {
            $otp->delete();

            // Create User
            $user = User::create([
                'name'     => $registrationData['name'],
                'phone'    => $registrationData['phone'] ?? null,
                'email'    => $registrationData['email'] ?? null,
                'password' => Hash::make($registrationData['password']),
                'role'     => User::$ROLE_MERCHANT,
            ]);

            // Create Merchant
            $merchant = Merchant::create([
                'uuid'              => Merchant::generateUniqueUuid(),
                'name'              => $registrationData['name'],
                'phone'             => $registrationData['phone'] ?? null,
                'shop_address'      => $registrationData['shop_address'],
                'shop_name'         => $registrationData['shop_name'],
                'slug'              => $registrationData['shop_name'] ? Str::slug($registrationData['shop_name']) : null,
                'shop_url'          => $registrationData['shop_url'],
                'shop_status'       => 0,
                'is_popular_enable' => 0, // default merchant is not popular
            ]);

            //create merchant user
            DB::table('shop_users')->insert([
                'user_id' => $user->id,
                'shop_id' => $merchant->id,
            ]);

            // Call the service to insert default data
            $this->userDefaultDataService->insert($merchant);

            // Generate authentication token (same as login)
            $tokenResult       = $user->createToken('auth_token');
            $token             = $tokenResult->accessToken;
            $expiresAt         = now()->addDays(1);
            $token->expires_at = $expiresAt;
            $token->save();

            // Fetch delivery fees (same as login)
            $keyMap = [
                'shipping_fee_exd' => 'ed_delivery_fee',
                'shipping_fee_isd' => 'id_delivery_fee',
                'shipping_fee_osd' => 'od_delivery_fee',
            ];

            $settings     = ShopSetting::whereIn('key', array_keys($keyMap))->pluck('value', 'key');
            $shippingData = [];

            foreach ($keyMap as $dbKey => $label) {
                $shippingData[$label] = $settings[$dbKey] ?? 0;
            }

            // Remove cached data
            Cache::forget($cacheKey);

            DB::commit();
            DB::connection('mysql_external')->commit();

            $info = ShopSetting::whereIn('key', ['contact_number', 'contact_email'])->pluck('value', 'key');

            $currentHost = request()->getHost();

            if (filter_var($currentHost, FILTER_VALIDATE_IP)) {
                $rootDomain = $currentHost;
            } else {
                $hostParts  = explode('.', $currentHost);
                $rootDomain = implode('.', array_slice($hostParts, -2));
            }

            $domain = request()->getScheme() . '://' . $rootDomain;

            // Return response identical to login
            return ApiResponse::success('Merchant Registration and Login Successful', [
                'name' => $user->name,
                'user' => [
                    'id'          => $user->id,
                    'role'        => $user->role->value == User::$ROLE_MERCHANT ? 'Merchant' : '',
                    'name'        => $user->role->value == User::$ROLE_MERCHANT ? 'Merchant' : '',
                    'shop_name'   => $user->merchant ? $user->merchant->shop_name : '',
                    'merchant_id' => $user->merchant ? $user->merchant->id : '',
                    'phone'       => $user->phone,
                    'address'     => $user->merchant->shop_address ?? '',
                ],
                'token'         => $tokenResult->plainTextToken,
                'image'         => $user->image ?? '',
                'delivery_fees' => $shippingData,
                'expires_at'    => $expiresAt->toDateTimeString(),
                'info'          => [
                    'phone'          => $info['contact_number'] ?? '',
                    'email'          => $info['contact_email']  ?? '',
                    'packly_website' => $domain,
                ],
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollBack();
            DB::connection('mysql_external')->rollBack();
            Log::error('Error in registerVerifyOtp: ' . $e->getMessage());

            return ApiResponse::failure('Registration and login failed ', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /*
     * Reset password using phone
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $request->validated();

        // Validate OTP
        $otp = Otp::where('phone', $request->phone)
            ->where('otp', $request->otp)
            ->where('is_verified', 0)
            ->first();

        if (! $otp) {
            return ApiResponse::failure('Invalid action. your activity will be reported.');
        }

        $mobileNumber = $request->phone;

        // Find user by mobile number
        $user = User::where('phone', $mobileNumber)->whereIn('role', [User::$ROLE_MERCHANT, User::$SHOP_ADMIN])->first();

        if ($user) {
            $user->password = Hash::make($request->password);
            $user->save();

            // Revoke all existing tokens after password reset (no current token in this flow)
            $user->tokens()->delete();

            $otp->is_verified = 1;
            $otp->save();

            return ApiResponse::success('Password reset successfully.', null, Response::HTTP_OK);
        }

        return ApiResponse::failure('User not found.');
    }

    /**
     * Validate the token from the request.
     */
    private function validateToken(Request $request): ?Model
    {
        $token = $request->token;

        if (! $token) {
            return null;
        }

        $accessToken = PersonalAccessToken::findToken($token);

        if (! $accessToken) {
            return null;
        }

        // Here tokenable is the User model
        return $accessToken->tokenable;
    }


    private function getUserPermissions(User $user): array
    {
        $permissions = [];

        switch ($user->role->value) {
            case UserRole::MERCHANT->value:
                $permissions = Permission::where('guard_name', 'api')
                    ->pluck('name')
                    ->toArray();
                break;

            case UserRole::SHOP_ADMIN->value:
                $user->guard_name = 'api';
                $permissions = $user->getAllPermissions()
                    ->pluck('name')
                    ->toArray();
                break;

            default:
                $permissions = [];
                break;
        }

        return $permissions;
    }

    private function getUserRole(User $user): string
    {
        $user->guard_name = 'api';
        $roles = $user->getRoleNames();

        return $roles->isNotEmpty() ? $roles->first() : null;
    }
    public function disableMerchant(Request $request)
    {
        $user = $request->user();
        if (! $user) {
            return ApiResponse::failure('Invalid token.');
        }
        if ($user->role->value !== UserRole::MERCHANT->value) {
            return ApiResponse::failure('You are not authorized to perform this action.');
        }
        $user->status = '0';
        $user->save();

        return ApiResponse::success('Merchant disabled successfully.', null, Response::HTTP_OK);
    }
}
