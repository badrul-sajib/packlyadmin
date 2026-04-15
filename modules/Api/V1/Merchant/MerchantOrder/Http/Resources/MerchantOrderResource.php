<?php

namespace Modules\Api\V1\Merchant\MerchantOrder\Http\Resources;

use App\Enums\OrderStatus;
use App\Http\Resources\Merchant\OrderItemResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MerchantOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $order = $this->order;
        $payment = $this->payment;
        $notAllowed = in_array($this->status_id, [OrderStatus::CANCELLED->value, OrderStatus::RETURN_REQUEST->value, OrderStatus::RETURNED->value, OrderStatus::REFUNDED->value, OrderStatus::UNKNOWN->value]);
        $commission = $this->items()->where('status_id', OrderStatus::DELIVERED->value)->sum('commission');

        $gatewayCharge = $this->gatewayCharge();

        $discountAmount = $this->bear_by_packly == 1 ? $this->discount_amount : 0;

        return [
            'id' => $this->id,
            'invoice_id' => $order->invoice_id ?? null,
            'invoice_number' => $this->invoice_id ?? null,
            'tracking_id' => $this->tracking_id,
            'consignment_id' => $this->consignment_id ?? null,
            'customer' => [
                'name' => $order->customer_name ?? null,
                'phone' => $order->customer_number ?? null,
                'address' => $order->customer_address ?? null,
                'landmark' => $order->customer_landmark ?? null,
            ],
            'items' => OrderItemResource::collection($this->items),
            'shipping_type' => $order->shipping_type ?? null,
            'shipping_amount' => $this->shipping_amount,
            'total_amount' => $this->total_amount,
            'sub_total' => $this->sub_total,
            'grand_total' => $this->grand_total,
            'merchant_grand_total' => $this->grand_total - $this->shipping_amount,
            'delivery_type' => $order->delivery_type ?? null,
            'payment_status' => $payment->payment_status ?? null,
            'discount_amount' => $this->discount_amount,
            'bear_by_packly' => $this->bear_by_packly,
            'charge' => $this->charge,
            'status_id' => $this->status_id,
            'courier_status' => $this->courier_status,
            'payment_method' => $payment->payment_method ?? null,
            'transaction_id' => $payment->tran_id ?? null,
            'payment_ref' => $payment->payment_ref ?? null,
            'created_at' => dateFormat($this->created_at),
            'commission' => $commission,
            'gateway_charge' => $gatewayCharge,
            'payout_paid' => $this->payoutPaid(),
            'merchant_will_receive' => $notAllowed ? 0 : max(0, $this->grand_total - $this->shipping_amount - $commission - $gatewayCharge + $discountAmount),
        ];
    }
}
