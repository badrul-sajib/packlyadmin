<?php

namespace App\Http\Controllers\Admin\Payment;

use App\Http\Controllers\Controller;
use App\Models\Payment\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:payment-method-list')->only('index');
        $this->middleware('permission:payment-method-update')->only('update');
    }

    public function index()
    {
        $methods = PaymentMethod::all();

        return view('Admin::payment-methods.index', compact('methods'));
    }

    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $data = $request->validate([
            'name'      => ['required', 'string', 'max:100'],
            'is_active' => ['required', 'boolean'],
            'image'     => ['nullable', 'file', 'mimes:jpeg,png,jpg,gif,svg,avif,webp,ico'],
        ]);

        $paymentMethod->name      = $data['name'];
        $paymentMethod->is_active = $data['is_active'];

        if ($request->hasFile('image')) {
            $paymentMethod->image = $request->file('image');
        }

        $paymentMethod->save();

        return redirect()->route('admin.payment-methods.index')
            ->with('success', 'Payment method updated successfully.');
    }
}
