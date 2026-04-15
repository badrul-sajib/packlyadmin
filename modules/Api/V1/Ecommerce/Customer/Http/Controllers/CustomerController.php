<?php

namespace Modules\Api\V1\Ecommerce\Customer\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Api\V1\Ecommerce\Customer\Http\Requests\CustomerAddressSendOtpRequest;
use Modules\Api\V1\Ecommerce\Customer\Http\Requests\CustomerAddressRequest;
use Modules\Api\V1\Ecommerce\Customer\Http\Requests\CustomerAddressVerifyOtpRequest;
use Modules\Api\V1\Ecommerce\Customer\Http\Requests\OrderCancelRequest;
use Modules\Api\V1\Ecommerce\Customer\Http\Requests\OrderReturnRequest;
use Modules\Api\V1\Ecommerce\Customer\Http\Requests\PaymentSuccessRequest;
use App\Models\Order\OrderPayment;
use App\Models\Payment\EPayment;
use App\Services\CustomerAddressService;
use App\Services\CustomerOrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class CustomerController extends Controller
{
    public function __construct(private readonly CustomerAddressService $customerAddressService) {}

    public function customerAddressSendOtp(CustomerAddressSendOtpRequest $request): JsonResponse
    {
        return $this->customerAddressService->sendOtpForAddressPhone($request->validated()['contact_number']);
    }

    public function customerAddressVerifyOtp(CustomerAddressVerifyOtpRequest $request): JsonResponse
    {
        $data = $request->validated();

        return $this->customerAddressService->verifyOtpForAddressPhone($data['contact_number'], $data['otp']);
    }

    public function customerAddressStore(CustomerAddressRequest $request): JsonResponse
    {
        $data = $request->validated();

        return $this->customerAddressService->create($data);
    }

    public function customerAddressList(): JsonResponse
    {
        return $this->customerAddressService->getAll();
    }

    public function customerAddressUpdate(CustomerAddressRequest $request, $id): JsonResponse
    {
        $data = $request->validated();

        return $this->customerAddressService->update($id, $data);
    }

    public function customerOrders(Request $request)
    {
        return CustomerOrderService::getCustomerOrder($request);
    }

    public function customerOrderDetails($tracking_id)
    {
        return CustomerOrderService::getCustomerOrderDetails($tracking_id);
    }

    public function cancelOrderItem(OrderCancelRequest $request, $tracking_id): JsonResponse
    {
        $data = $request->validated();

        return CustomerOrderService::OrderItemCancel($data, $tracking_id);
    }

    public function returnOrderItem(OrderReturnRequest $request, $tracking_id): JsonResponse
    {
        $data = $request->validated();

        return CustomerOrderService::OrderItemReturn($data, $tracking_id);
    }

    public function customerReturns(Request $request): JsonResponse
    {
        return CustomerOrderService::getCustomerReturns();
    }

    public function returnDetails($item_id, $tracking_id): JsonResponse
    {
        return CustomerOrderService::getCustomerReturnDetails($item_id, $tracking_id);
    }

    public function paymentSuccess(PaymentSuccessRequest $request)
    {
        $validated = $request->validated();

        $ePayment = EPayment::create([
            'order_id' => $validated['tran_id'],
            'amount'   => $validated['amount'],
        ]);

        if ($ePayment->orderPayments->isNotEmpty()) {
            foreach ($ePayment->orderPayments as $orderPayment) {
                $orderPayment->payment_status = OrderPayment::$PAID; // Paid
                $orderPayment->save();
            }
        }

        return response()->json([
            'message' => 'Payment recorded successfully',
            'data'    => $validated,
        ], Response::HTTP_CREATED);
    }

    public function customerGtmInfo(): JsonResponse
    {
        $user = Auth::user();

        return success('Customer info fetched successfully', [
            'name'          => $user->name,
            'email'         => $user->email,
            'phone'         => $user->phone,
            'name_hash'     => Hash::make($user->name),
            'email_hash'    => Hash::make($user->email),
            'phone_hash'    => Hash::make($user->phone),
            'total_orders'  => $user->orders()->count(),
            'customer_type' => $user->orders()->count() > 0 ? 'returning' : 'new',
            'total_spent'   => $user->orders()->sum('grand_total'),
        ]);
    }
}
