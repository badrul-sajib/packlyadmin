<?php

namespace App\Services\Checkout;

use App\DTOs\CheckoutData;
use App\Jobs\SendSMS;
use App\Models\Order\CustomerAddress;
use App\Services\Checkout\Calculators\CouponDiscount;
use App\Services\Checkout\Calculators\PriceCalculator;
use App\Services\Checkout\Calculators\ShippingCalculator;
use App\Services\Checkout\Processors\MerchantOrderProcessor;
use App\Services\Checkout\Processors\OrderProcessor;
use App\Services\Checkout\Processors\RemoveCart;
use App\Services\Checkout\Validators\DeliveryTypeValidator;
use App\Services\Checkout\Validators\PreventDuplicateOrder;
use App\Services\Checkout\Validators\StockValidator;
use App\Services\Coupon\CouponCheckoutValidator;
use App\Services\GiveawayService;
use App\Services\Payments\PaymentFactory;
use App\Traits\Transaction;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class CheckoutService
{
    public function __construct(
        private readonly ShippingCalculator $shippingCalculator,
        private readonly PriceCalculator $priceCalculator,
        private readonly StockValidator $stockValidator,
        private readonly OrderProcessor $orderProcessor,
        private readonly MerchantOrderProcessor $merchantOrderProcessor,
        private CouponDiscount $couponDiscount,
        private readonly DeliveryTypeValidator $deliveryTypeValidator,
        private readonly CouponCheckoutValidator $couponValidator,
        private readonly PreventDuplicateOrder $preventDuplicateOrder,
    ) {}

    public function checkout(array $data): JsonResponse
    {
        try {
            return Transaction::rollback(fn () => $this->processCheckout($data));
        } catch (ModelNotFoundException $e) {
            return failure('Checkout failed.', Response::HTTP_NOT_FOUND);
        } catch (ValidationException $e) {
            // Log::error($e);

            return failure('Checkout failed.', Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            // Log::error($e);

            return failure('Checkout failed.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @throws Throwable
     */
    private function processCheckout(array $data): JsonResponse
    {
        $checkoutData = CheckoutData::fromArray($data); // Validate checkout data

        if (config('app.env') != 'local') {
            $this->preventDuplicateOrder->preventDuplicateOrder($checkoutData); // prevent duplicate
        }

        $customerAddress = CustomerAddress::findOrFail($checkoutData->customerAddressId);

        // customer validate
        $this->customerAddressValidation($customerAddress);

        // Validate stock before processing
        $this->stockValidator->validate($checkoutData);
        $this->deliveryTypeValidator->validate($checkoutData->deliveryType); // Validate delivery type

        // Calculate prices and shipping
        $orderItems = $this->priceCalculator->calculatePrices($checkoutData);
        $shippingDetails = $this->shippingCalculator->calculate($customerAddress, $orderItems, $checkoutData->deliveryType); // Calculate shipping cost

        // coupon validate and calculate discount
        $coupon = $this->couponValidator->validate($data);

        // Create order and related records
        $order = $this->orderProcessor->createOrder($checkoutData, $customerAddress, $orderItems, $shippingDetails, $coupon);
        $merchantOrders = $this->merchantOrderProcessor->createMerchantOrders($order, $orderItems, $shippingDetails, $coupon);

        $isSpam =(new \App\Services\Order\SpamOrderService)->checkOrder($order);
        if($isSpam == null) {
            foreach ($merchantOrders as  $merchantOrder) {
                if(isset($merchantOrder->merchant) && $merchantOrder->merchant->phone) {
                    $sub_total          = $merchantOrder->sub_total ?? 0;
                    if($merchantOrder->bear_by_packly == null) $sub_total -= $merchantOrder->discount_amount;

                    $notificationMerchant = '
New Order Received!
Order #'.$merchantOrder->invoice_id.' 
Price ৳'.$sub_total.' has been placed.
Please process it from your dashboard & prepare for shipment.
 
Packly
                    ';
                    SendSMS::dispatch($merchantOrder->merchant->phone, $notificationMerchant);
                }
            }
        }
        

        // Process payment
        $paymentProcessor = PaymentFactory::getProcessor($checkoutData->paymentMethod);
        $paymentResult = $paymentProcessor->process($order, $data);

        // Remove cart
        (new RemoveCart($order))();

        if ($data['payment_method'] === 'COD') {
            $customerName = $order->customer_name ?? 'Customer';
            $notification = 'Dear '.$customerName.', Your order has been placed successfully. Track your order from packly. Your order Id is : #'.$order->invoice_id;
            SendSMS::dispatch($order->customer_number, $notification);
        }

        // info('Order placed successfully', [
        //     'order'   => $order,
        //     'payment' => $paymentResult,
        // ]);

        (new GiveawayService)->generateGiveawayTicket($order);

        return success('Order placed successfully', [
            'order' => $order,
            'payment' => $paymentResult,
        ], Response::HTTP_CREATED);
    }

    protected function customerAddressValidation($customerAddress): void
    {
        if ($customerAddress->user_id !== auth()->id()) {
            throw ValidationException::withMessages(['customer_address_id' => 'Invalid customer address']);
        }
    }
}
