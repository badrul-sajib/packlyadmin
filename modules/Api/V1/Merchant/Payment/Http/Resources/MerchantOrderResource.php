<?php

namespace Modules\Api\V1\Merchant\Payment\Http\Resources;

use App\Enums\OrderStatus;
use App\Http\Resources\Merchant\OrderItemResource;
use Carbon\Carbon;
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

        // Ensure items relation is loaded safely
        $items = $this->relationLoaded('items') ? $this->items : collect([]);

        // Check if order is not allowed for payout
        $notAllowed = in_array($this->status_id, [
            OrderStatus::CANCELLED->value,
            OrderStatus::RETURN_REQUEST->value,
            OrderStatus::RETURNED->value,
            OrderStatus::REFUNDED->value,
            OrderStatus::UNKNOWN->value
        ]);

        // Commission calculation from delivered items
        $commission = $items->where('status_id', OrderStatus::DELIVERED->value)->sum('commission');

        // Gateway charge safely
        $gatewayCharge = $this->gatewayCharge();

        // Payout eligibility calculation
        $baseDate = $this->delivered_at ?? $this->updated_at;
        $shopSettings = $this->merchant->getShopSettings();
        $days = (int) ($shopSettings['payout_request_date'] ?? 0);

        $eligibleDate = Carbon::parse($baseDate)->addDays($days)->startOfDay();
        $today = now()->startOfDay();
        $payoutWillEligible = max(0, $today->diffInDays($eligibleDate, false));

        $this->discount_amount = $this->bear_by_packly == 1 ? 0 : $this->discount_amount;

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
            'items' => OrderItemResource::collection($items),
            'shipping_type' => $order->shipping_type ?? null,
            'shipping_amount' => $this->shipping_amount,
            'total_amount' => $this->total_amount,
            'sub_total' => $this->sub_total - $this->discount_amount,
            'merchant_sub_total' => $this->sub_total,
            'grand_total' => $this->grand_total,
            'delivery_type' => $order->delivery_type ?? null,
            'payment_status' => $payment->payment_status ?? null,
            'discount_amount' => $this->discount_amount,
            'charge' => $this->charge,
            'status_id' => $this->status_id,
            'courier_status' => $this->courier_status,
            'payment_method' => $payment->payment_method ?? null,
            'transaction_id' => $payment->tran_id ?? null,
            'payment_ref' => $payment->payment_ref ?? null,
            'created_at' => dateFormat($this->created_at),
            'delivered_at' => dateFormat($this->delivered_at ?? $this->updated_at),
            'commission' => $commission,
            'gateway_charge' => $gatewayCharge,
            'payout_will_eligible' => $payoutWillEligible,
            'bear_by_packly' => $this->bear_by_packly,
            'merchant_will_receive' => $notAllowed
                ? 0
                : max(0, $this->sub_total - $this->discount_amount - $commission - $gatewayCharge),
                'payout' => $this->payout ? PayoutResource::make($this->payout) : null
        ];
    }
}
