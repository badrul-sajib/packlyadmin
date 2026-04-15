<?php

namespace App\Http\Controllers\Admin\Voucher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\VoucherRequest;
use App\Services\Voucher\VoucherServices;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    protected VoucherServices $voucherServices;

    public function __construct(VoucherServices $voucherServices)
    {
        $this->voucherServices = $voucherServices;
        $this->middleware('permission:voucher-list')->only('index');
        $this->middleware('permission:voucher-create')->only(['create', 'store']);
        $this->middleware('permission:voucher-update')->only(['edit', 'update']);
        $this->middleware('permission:voucher-delete')->only('destroy');
    }

    public function index(Request $request)
    {
        $vouchers  = $this->voucherServices->getAllVouchers($request);
        $startDate = $request->start_date ?? now()->subDays(6)->startOfDay();
        $endDate   = $request->end_date   ?? now()->endOfDay();

        return customView(['ajax' => 'components.voucher.table', 'default' => 'Admin::vouchers.index'], compact('vouchers', 'startDate', 'endDate'));
    }

    public function create()
    {
        return view('Admin::vouchers.create');
    }

    public function store(VoucherRequest $request)
    {
        return $this->voucherServices->createVoucher($request);
    }

    public function edit(int $id)
    {
        $voucher = $this->voucherServices->getVoucherById($id);

        return view('Admin::vouchers.edit', compact('voucher'));
    }

    public function update(VoucherRequest $request, int $id)
    {
        return $this->voucherServices->updateVoucher($request, $id);
    }

    public function destroy(int $id)
    {
        $this->voucherServices->deleteVoucher($id);

        return response()->json(['success' => 'Voucher deleted successfully']);
    }
}
