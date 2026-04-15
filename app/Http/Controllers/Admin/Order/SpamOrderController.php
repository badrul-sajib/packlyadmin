<?php

namespace App\Http\Controllers\Admin\Order;

use App\Jobs\SendSMS;
use App\Models\Order\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Order\SpamOrderService;

class SpamOrderController extends Controller
{
    public function __construct(protected SpamOrderService $spamOrderService) {}

    public function index(Request $request)
    {
        $orders = $this->spamOrderService->getSpamOrders($request);

        if ($request->ajax()) {
            return view('components.orders.spam_table', ['entity' => $orders])->render();
        }

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        return view('Admin::orders.spam_index', compact('orders', 'startDate', 'endDate'));
    }

    public function approve(Order $order)
    {
        $order->update(['is_spam' => false]);


        foreach ($order->merchantOrders as  $merchantOrder) {
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

        return redirect()->route('admin.spam.orders.index')->with('success', 'Selected spam orders have been approved.');
    }
}
