<?php

namespace App\Services;

use App\Models\Order\CustomerAddress;
use App\Models\User\Otp;
use App\Traits\Transaction;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CustomerAddressService
{
    protected CustomerAddress $model;

    public function __construct(CustomerAddress $model)
    {
        $this->model = $model;
    }

    public function getAll(): JsonResponse
    {
        try {
            // Fetch user addresses with related locations and their parent hierarchy
            $addresses = auth()->user()->addresses()
                ->with(['location.parent.parent']) // Eager load relationships
                ->select(
                    'id',
                    'name',
                    'landmark',
                    'address',
                    'address_type',
                    'contact_number',
                    'status',
                    'is_default_bill',
                    'is_default_ship',
                    'location_id'
                )
                ->get();

            // Map the addresses to include calculated fields
            $addressList = $addresses->map(function ($address) {
                $addr = $address->address;

                if (preg_match('/[\x80-\xff]/', $addr)) {
                    $addr = banglaToBanglish($addr);
                }

                $insideDhaka = (new InsideDhakaService)->isInsideDhaka($addr);
                $shipType    = $insideDhaka ? 'ISD' : 'OSD';

                $city     = $address->location;
                $district = $city?->parent;
                $division = $district?->parent;

                return [
                    'id'              => (int) $address?->id,
                    'name'            => ($address->name !== 'N/A') ? $address->name : null,
                    'landmark'        => $address->landmark,
                    'address'         => $address->address,
                    'address_type'    => $address->address_type,
                    'contact_number'  => $address->contact_number,
                    'status'          => (int) $address->status,
                    'is_default_bill' => (int) $address->is_default_bill,
                    'is_default_ship' => (int) $address->is_default_ship,
                    'city'            => [
                        'id'   => $city?->id,
                        'name' => $city?->name,
                    ],
                    'district' => [
                        'id'   => (int) $district?->id,
                        'name' => $district?->name,
                    ],
                    'division' => [
                        'id'   => (int) $division?->id,
                        'name' => $division?->name,
                    ],
                    'ship_type' => $shipType,
                ];
            });

            return success('Address list', $addressList);
        } catch (Exception $e) {
            $status = $e->getCode() === Response::HTTP_UNPROCESSABLE_ENTITY
                ? Response::HTTP_UNPROCESSABLE_ENTITY
                : Response::HTTP_INTERNAL_SERVER_ERROR;

            return failure($e->getMessage() ?: 'Request failed', $status);
        }
    }

    public function create(array $data = []): JsonResponse
    {
        try {
            $user = auth()->user();
            // $this->ensurePhoneOtpVerified($data['contact_number']); :: TODO

            if (isset($data['is_default_ship']) && $data['is_default_ship'] == 1) {
                $user->addresses()->update(['is_default_ship' => 0]);
            }

            $address = $user->addresses()->create($data);

            if (preg_match('/[\x80-\xff]/', $address->address)) {
                $address->address = banglaToBanglish($address->address);
            }

            $insideDhaka = (new InsideDhakaService)->isInsideDhaka($address->address);

            // Fetch shipping settings once
            $settings = DB::table('shop_settings')
                ->whereIn('key', ['shipping_isd', 'shipping_fee_osd', 'shipping_fee_isd'])
                ->pluck('value', 'key');

            $osdFee = (int) ($settings['shipping_fee_osd'] ?? 0);
            $isdFee = (int) ($settings['shipping_fee_isd'] ?? 0);

            $shipType   = $insideDhaka ? 'ISD' : 'OSD';
            $shipAmount = $shipType === 'ISD' ? $isdFee : $osdFee;



            Cache::forget($this->getAddressPhoneOtpCacheKey((int) $user->id, $data['contact_number']));

            return success('Address added successfully', [
                'id'              => (int) $address->id,
                'name'            => $address->name,
                'contact_number'  => $address->contact_number,
                'ship_type'       => $shipType,
                'ship_amount'     => (int) $shipAmount,
                'is_default_ship' => (int) $address->is_default_ship,
            ], 201);
        } catch (Exception $e) {
            $status = $e->getCode() === Response::HTTP_UNPROCESSABLE_ENTITY
                ? Response::HTTP_UNPROCESSABLE_ENTITY
                : Response::HTTP_INTERNAL_SERVER_ERROR;

            return failure($e->getMessage() ?: 'Request failed', $status);
        }
    }

    // update
    public function update(int $id, array $data = []): JsonResponse
    {
        try {
            return Transaction::retryAndRollback(function () use ($id, $data) {

                $user = auth()->user();
                $address = $user->addresses()->find($id);

                if (! $address) {
                    return failure('Address not found', Response::HTTP_NOT_FOUND);
                }

                $currentPhone = $address->contact_number;
                $newPhone = $data['contact_number'] ?? $currentPhone;

                // if ($newPhone !== $currentPhone) {
                //     $this->ensurePhoneOtpVerified($newPhone); :: TODO
                // }

                if (isset($data['is_default_bill']) && $data['is_default_bill'] == 1) {
                    $user->addresses()->update(['is_default_bill' => 0]);
                }

                if (isset($data['is_default_ship']) && $data['is_default_ship'] == 1) {
                    $user->addresses()->update(['is_default_ship' => 0]);
                }

                $user->addresses()->where('id', $id)->update($data);
                $address = $user->addresses()->find($id);

                if (preg_match('/[\x80-\xff]/', $address->address)) {
                    $address->address = banglaToBanglish($address->address);
                }

                $insideDhaka = (new InsideDhakaService)->isInsideDhaka($address->address);

                $shipType   = $insideDhaka ? 'ISD' : 'OSD';

                $user->update([
                    'name' => $address->name,
                ]);

                if ($newPhone !== $currentPhone) {
                    Cache::forget($this->getAddressPhoneOtpCacheKey((int) $user->id, $newPhone));
                }

                return success('Address updated successfully', [
                    'id'              => (int) $address->id,
                    'name'            => $address->name,
                    'contact_number'  => $address->contact_number,
                    'ship_type'       => $shipType,
                    'is_default_ship' => (int) $address->is_default_ship,
                ], Response::HTTP_OK);
            });
        } catch (Exception $e) {
            $status = $e->getCode() === Response::HTTP_UNPROCESSABLE_ENTITY
                ? Response::HTTP_UNPROCESSABLE_ENTITY
                : Response::HTTP_INTERNAL_SERVER_ERROR;

            return failure($e->getMessage() ?: 'Request failed', $status);
        }
    }

    public function sendOtpForAddressPhone(string $phone): JsonResponse
    {
        try {
            $existingOtp = Otp::where('phone', $phone)->where('is_verified', 0)->first();

            if ($existingOtp && ! Carbon::now()->greaterThan($existingOtp->expires_at)) {
                return success('Please wait before requesting a new OTP.', null);
            }

            $code = rand(100000, 999999);
            $expiresAt = Carbon::now()->addMinutes(2);

            Otp::updateOrCreate(
                ['phone' => $phone],
                [
                    'otp' => $code,
                    'expires_at' => $expiresAt,
                    'is_verified' => 0,
                ]
            );

            (new SmsService)->sendMessage(
                $phone,
                "Packly - Your OTP is $code. It will expire in 2 minutes."
            );

            Cache::forget($this->getAddressPhoneOtpCacheKey((int) auth()->id(), $phone));

            return success('OTP sent successfully.', null);
        } catch (Exception $e) {
            return failure('Failed to send OTP', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function verifyOtpForAddressPhone(string $phone, string $otp): JsonResponse
    {
        try {
            $otpRecord = Otp::where('phone', $phone)
                ->where('otp', $otp)
                ->where('is_verified', 0)
                ->first();

            if (! $otpRecord) {
                return failure('Invalid OTP.', Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            if (Carbon::now()->greaterThan($otpRecord->expires_at)) {
                return failure('OTP has expired. Please request a new OTP.', Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $otpRecord->is_verified = 1;
            $otpRecord->expires_at = null;
            $otpRecord->save();

            Cache::put($this->getAddressPhoneOtpCacheKey((int) auth()->id(), $phone), true, now()->addMinutes(10));

            return success('OTP verified successfully.', null);
        } catch (Exception $e) {
            return failure('Failed to verify OTP', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    // :: TODO
    // private function ensurePhoneOtpVerified(string $phone): void
    // {
    //     $key = $this->getAddressPhoneOtpCacheKey((int) auth()->id(), $phone);

    //     if (! Cache::get($key, false)) {
    //         throw new Exception('Phone number must be OTP verified first.', Response::HTTP_UNPROCESSABLE_ENTITY);
    //     }
    // }

    private function getAddressPhoneOtpCacheKey(int $userId, string $phone): string
    {
        return "customer_address_phone_verified_{$userId}_{$phone}";
    }
}
