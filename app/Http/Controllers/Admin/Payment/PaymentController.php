<?php

namespace App\Http\Controllers\Admin\Payment;

use App\Http\Controllers\Controller;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Throwable;

class PaymentController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
        $this->middleware('permission:order-payment-list')->only('index');
        $this->middleware('permission:order-payment-show')->only('show');
        $this->middleware('permission:order-payment-update')->only('changeStatus');
    }

    /**
     * @throws Throwable
     */
    public function index(Request $request)
    {
        $payments = $this->paymentService->getAllPayments($request);
        if ($request->ajax()) {
            return view('components.payment.table', ['entity' => $payments])->render();
        }

        return view('Admin::payments.index', compact('payments'));
    }

    public function changeStatus(Request $request, $id)
    {
        return $this->paymentService->paymentStatusChange($request, $id);
    }

    public function show($id)
    {
        $payment = $this->paymentService->getPaymentById($id);

        return view('Admin::payments.show', compact('payment'));
    }
}
