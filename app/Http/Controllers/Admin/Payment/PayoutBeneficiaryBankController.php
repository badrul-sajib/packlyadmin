<?php

namespace App\Http\Controllers\Admin\Payment;

use App\Http\Controllers\Controller;
use App\Models\Payment\PayoutBeneficiaryBank;
use Illuminate\Http\Request;

class PayoutBeneficiaryBankController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:payout-beneficiary-bank-list')->only('index');
        $this->middleware('permission:payout-beneficiary-bank-create')->only(['create', 'store']);
        $this->middleware('permission:payout-beneficiary-bank-update')->only(['edit', 'update']);
    }

    public function index(Request $request)
    {
        $perPage = $request->input('perPage', 10);
        $page = $request->input('page', 1);
        $search = $request->input('search', '');
        $banks = PayoutBeneficiaryBank::when($search, function ($query) use ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        })->
        orderByDesc('id')->paginate($perPage, ['*'], 'page', $page);
        return view('Admin::payments.bank.index', compact('banks'));
    }


    public function create()
    {
        return view('Admin::payments.bank.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:payout_beneficiary_banks,name',
        ]);

        PayoutBeneficiaryBank::create([
            'name' => $request->name
        ]);

        return redirect()->route('admin.payout-beneficiary-banks.index')->with('success', 'Bank created successfully.');
    }

    public function edit(PayoutBeneficiaryBank $payoutBeneficiaryBank)
    {
        return view('Admin::payments.bank.edit', compact('payoutBeneficiaryBank'));
    }

    public function update(Request $request, PayoutBeneficiaryBank $payoutBeneficiaryBank)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:payout_beneficiary_banks,name,' . $payoutBeneficiaryBank->id,
        ]);

        $payoutBeneficiaryBank->update([
            'name' => $request->name
        ]);

        return redirect()->route('admin.payout-beneficiary-banks.index')->with('success', 'Bank updated successfully.');
    }
}
