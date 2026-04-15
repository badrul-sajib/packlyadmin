<?php

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Models\Payment\SslcommerzPayment;
use Exception;
use Illuminate\Http\RedirectResponse;

class PaymentService
{
    public function getAllPayments($request)
    {
        $search  = $request->search;
        $type    = $request->input('type', 'division');
        $perPage = $request->input('perPage', 10);
        $page    = $request->input('page', 1);

        return SslcommerzPayment::query()
            ->with(['order'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('order', function ($q2) use ($search) {
                        $q2->where('invoice_id', 'like', "%{$search}%");
                    })
                        ->orWhere('sslcommerz_payments.transaction_id', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate($perPage, ['*'], 'page', $page)
            ->withQueryString();
    }

    public function paymentStatusChange($request, $id): RedirectResponse
    {

        $payment        = $this->getPaymentById($id);
        $order          = $payment->order;
        $merchantOrders = $order->merchantOrders()->get();

        switch ($request->status) {
            case 'VALID':
                $status = PaymentStatus::PAID;

                break;
            case 'FAILED':
                $status = PaymentStatus::FAILED;

                break;
            case 'CANCELLED':
                $status = PaymentStatus::CANCELLED;

                break;
            case 'UNATTEMPTED':
                $status = PaymentStatus::UNATTEMPTED;

                break;
            case 'EXPIRED':
                $status = PaymentStatus::EXPIRED;

                break;
            default:
                $status = PaymentStatus::UNKNOWN;

                break;
        }

        foreach ($merchantOrders as $merchantOrder) {
            $merchantOrder->payments()->update([
                'payment_ref'       => $payment->transaction_id,
                'payment_status'    => $status,
            ]);
        }

        if (! $payment) {
            return redirect()->back()->with('message', 'Payment not found');
        }

        try {

            $payment->payment_status = $request->status;
            $payment->save();

            return redirect()->back()->with('message', 'Payment status updated successfully');
        } catch (Exception) {
            return redirect()->back()->with('message', 'Something went wrong');
        }
    }

    public function getPaymentById($id)
    {
        return SslcommerzPayment::with([
            'order',
        ])->find($id);
    }
}
