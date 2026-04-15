<?php

namespace App\Http\Controllers\Admin\Coupon;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CouponRequest;
use App\Models\Coupon\Coupon;
use App\Services\Coupon\CouponServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class CouponController extends Controller
{
    protected CouponServices $couponServices;

    public function __construct(
        CouponServices $couponServices,
    ) {
        $this->couponServices = $couponServices;
        $this->middleware('permission:coupon-list')->only('index');
        $this->middleware('permission:coupon-create')->only(['create', 'store']);
        $this->middleware('permission:coupon-update')->only(['edit', 'update']);
        $this->middleware('permission:coupon-delete')->only('destroy');
    }

    public function index(Request $request)
    {
        $coupons    = $this->couponServices->getAllCoupons($request);
        $startDate  = $request->start_date ?? null;
        $endDate    = $request->end_date   ?? null;
        $activities = Activity::where('subject_type', Coupon::class)->latest()->limit(10)->get();

        return customView(['ajax' => 'components.coupon.table', 'default' => 'Admin::coupons.index'], compact('coupons', 'startDate', 'endDate', 'activities'));
    }

    public function create()
    {
        $categories   = DB::table('categories')->get();

        return view('Admin::coupons.create', compact('categories'));
    }

    public function store(CouponRequest $request)
    {
        return $this->couponServices->createCoupon($request);
    }

    public function edit(int $id)
    {
        $categories   = DB::table('categories')->get();
        $coupon       = $this->couponServices->getCouponById($id);
        $activities   = $coupon->activities()->where('log_name', 'coupon-update')->get();

        return view('Admin::coupons.edit', compact('categories', 'coupon', 'activities'));
    }

    public function update(CouponRequest $request, int $id)
    {
        return $this->couponServices->updateCoupon($request, $id);
    }

    public function show(int $id)
    {
        $coupon       = $this->couponServices->getCouponById($id);
        $couponUsages = $this->couponServices->getCouponUsages($id);

        if(request()->ajax()){
            return view('components.coupons.usage-table', ['entity' => $couponUsages]);
        }

        $stats = $this->couponServices->getCouponStats($id);

        return view('Admin::coupons.show', array_merge(compact('coupon', 'couponUsages'), $stats));
    }

    public function destroy(int $id)
    {
        $this->couponServices->deleteCoupon($id);

        return response()->json(['success' => 'Coupon deleted successfully']);
    }
}
