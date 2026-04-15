<?php

namespace Modules\Api\V1\Merchant\PickupAddress\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Api\V1\Merchant\PickupAddress\Http\Requests\PickupAddressRequest;
use Modules\Api\V1\Merchant\PickupAddress\Http\Requests\SendPickupAddressRequest;
use App\Models\PickupAddress\PickupAddress;
use App\Models\PickupAddress\PickupAddressRequest as PickupAddressRequestModel;
use App\Models\Shop\ShopSetting;
use App\Services\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class PickupAddressController extends Controller
{

    public function __construct()
    {
        $this->middleware('shop.permission:show-pickup-address')->only(['index', 'show', 'pickupAddressRequests', 'getPoliceStations']);
        $this->middleware('shop.permission:create-pickup-address')->only(['store']);
        $this->middleware('shop.permission:update-pickup-address')->only(['update']);
        $this->middleware('shop.permission:send-pickup-address-request')->only(['sendPickupAddressRequest']);
    }

    public function index(): JsonResponse
    {
        $pickupAddress = PickupAddress::where('merchant_id', auth()->user()->merchant->id)->get();
        return ApiResponse::success('Pickup address retrieved successfully.', $pickupAddress, Response::HTTP_OK);
    }

    public function store(PickupAddressRequest $request): JsonResponse
    {
        PickupAddress::create([
            'merchant_id' => auth()->user()->merchant->id,
            'police_station_name' => $request->police_station_name,
            'police_station_id' => $request->police_station_id,
            'city_id' => $request->city_id,
            'city_name' => $request->city_name,
            'address' => $request->address,
            'name' => $request->name,
            'contact_number' => $request->contact_number
        ]);

        return ApiResponse::success('Pickup address created successfully.', null, Response::HTTP_CREATED);
    }

    public function update(PickupAddressRequest $request, PickupAddress $pickupAddress): JsonResponse
    {
        $pickupAddress->update([
            'merchant_id' => auth()->user()->merchant->id,
            'police_station_name' => $request->police_station_name,
            'police_station_id' => $request->police_station_id,
            'city_id' => $request->city_id,
            'city_name' => $request->city_name,
            'address' => $request->address,
            'name' => $request->name,
            'contact_number' => $request->contact_number
        ]);

        return ApiResponse::success('Pickup address updated successfully.', null, Response::HTTP_OK);
    }

    public function show(PickupAddress $pickupAddress): JsonResponse
    {
        return ApiResponse::success('Pickup address retrieved successfully.', $pickupAddress, Response::HTTP_OK);
    }

    public function getPoliceStations(): JsonResponse
    {

        $sfcConfig = ShopSetting::whereIn('key', ['sfc_base_url', 'sfc_public_key', 'sfc_secret_key'])->pluck('value', 'key')->toArray();

        if (empty($sfcConfig['sfc_base_url']) || empty($sfcConfig['sfc_public_key']) || empty($sfcConfig['sfc_secret_key'])) {
            return ApiResponse::failure('SFC Configuration problem, please contact admin', Response::HTTP_NOT_FOUND);
        }

        $response = Http::withHeaders([
            'api-key' => $sfcConfig['sfc_public_key'],
            'secret-key' => $sfcConfig['sfc_secret_key'],
        ])->get($sfcConfig['sfc_base_url'] . '/police_stations');

        if (!$response->successful()) {
            return ApiResponse::error('Failed to retrieve police stations.', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = $response->json();

        return ApiResponse::success(
            'Police stations retrieved successfully.',
            [
                'police_stations' => $data['data'] ?? [],
            ],
            Response::HTTP_OK
        );
    }

    public function sendPickupAddressRequest(SendPickupAddressRequest $request, PickupAddress $pickupAddress): JsonResponse
    {
        $sfcConfig = ShopSetting::whereIn('key', ['sfc_base_url', 'sfc_public_key', 'sfc_secret_key'])->pluck('value', 'key')->toArray();

        if (empty($sfcConfig['sfc_base_url']) || empty($sfcConfig['sfc_public_key']) || empty($sfcConfig['sfc_secret_key'])) {
            return ApiResponse::failure('SFC Configuration problem, please contact admin', Response::HTTP_NOT_FOUND);
        }

        $response = Http::withHeaders([
            'api-key' => $sfcConfig['sfc_public_key'],
            'secret-key' => $sfcConfig['sfc_secret_key'],
        ])->post($sfcConfig['sfc_base_url'] . '/create_pickup_request', [
                    'address_id' => $pickupAddress->id,
                    'police_station_id' => $pickupAddress->police_station_id,
                    'address' => $pickupAddress->address,
                    'contact_number' => $pickupAddress->contact_number,
                    'note' => $request->note,
                    'estim_qty' => $request->estim_qty
                ]);

        $data = $response->json(); // 👈 decode automatically

        if ($response->successful()) {
            PickupAddressRequestModel::create([
                'pickup_address_id' => $pickupAddress->id,
                'steadfast_pickup_request_id' => $data['data']['id'],
                'merchant_id' => auth()->user()->merchant->id,
                'note' => $request->note,
                'estim_qty' => $request->estim_qty
            ]);

            return ApiResponse::success('Pickup address request sent successfully.', null, Response::HTTP_OK);
        }

        // ⛔ If API returns pickup already exists error
        if (isset($data['code']) && $data['code'] === 'PICKUP_REQUEST_EXISTS') {
            return ApiResponse::failure($data['message'] ?? 'Pickup request already exists.', Response::HTTP_CONFLICT);
        }

        // Other unknown errors
        return ApiResponse::error($data['message'] ?? 'Failed to send pickup address request.', Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function pickupAddressRequests(): JsonResponse
    {
        $pickupAddressRequests = PickupAddressRequestModel::where('merchant_id', auth()->user()->merchant->id)->with('pickupAddress')->paginate();

        return ApiResponse::formatPagination('Pickup address requests retrieved successfully.', $pickupAddressRequests, Response::HTTP_OK);
    }
}
