<?php

namespace App\Http\Controllers\Admin\Shop;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PopularShopRequest;
use App\Models\Merchant\Merchant;
use App\Models\Shop\PopularShop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Spatie\Activitylog\Models\Activity;
use Throwable;

class PopularShopController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:popular-shop-list')->only('index');
        $this->middleware('permission:popular-shop-create')->only('store');
        $this->middleware('permission:popular-shop-update')->only('updateStatus', 'updateOrder');
        $this->middleware('permission:popular-shop-delete')->only('destroy');
    }

    /**
     * @throws Throwable
     */
    public function index(Request $request)
    {
        $perPage       = $request->input('perPage', 10);
        $page          = $request->input('page', 1);
        $search_type   = $request->input('search_type', 'id');
        $search        = $request->input('search');
        $status_filter = $request->input('status_filter', 'all');

        $popularShops = PopularShop::query()
            ->when($search, function ($query) use ($search, $search_type) {
                if ($search_type === 'id') {
                    $query->whereHas('merchant', function ($q) use ($search) {
                        $q->where('id', $search);
                    });
                } else {
                    $query->whereHas('merchant', function ($q) use ($search) {
                        $q->where('shop_name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
                }
            })
            ->when($status_filter !== 'all', function ($query) use ($status_filter) {
                if ($status_filter === 'active') {
                    $query->where('is_active', 1);
                } elseif ($status_filter === 'inactive') {
                    $query->where('is_active', 0);
                }
            })
            ->orderBy('display_order')
            ->paginate($perPage, ['*'], 'page', $page);

        if ($request->ajax()) {
            return view('components.popular_shop.table', [
                'entity' => $popularShops,
            ])->render();
        }

        $activities = Activity::where('log_name', 'Popular-Shop-Enable-Status-Update')
            ->latest()
            ->take(20)
            ->get();

        return view('backend.pages.popular_shops.index', compact('popularShops', 'activities'));
    }

    public function searchMerchants(Request $request)
    {
        $keyword = $request->get('keyword');
        $type    = $request->get('type', 'other'); // default to "other"

        // Get all merchant IDs that are already in popular_shops
        $popularMerchantIds = PopularShop::pluck('merchant_id');

        $query = Merchant::query()
            ->active()
            ->select('id', 'shop_name', 'phone')
            ->whereNotIn('id', $popularMerchantIds);

        // ✅ If no keyword provided → show 20 merchants by default
        if (blank($keyword)) {
            return $query->orderBy('id', 'desc')->limit(20)->get();
        }

        // ✅ If search type is 'id', only match ID exactly
        if ($type === 'id') {
            $query->where('id', $keyword);
        }
        // ✅ Otherwise, match other fields loosely
        else {
            $query->where(function ($q) use ($keyword) {
                $q->where('shop_name', 'like', "%{$keyword}%")
                    ->orWhere('phone', 'like', "%{$keyword}%");
            });
        }

        return $query->orderBy('id', 'desc')->limit(30)->get();
    }

    public function store(Request $request)
    {
        $merchantIds = $request->input('merchant_ids', []);
        foreach ($merchantIds as $id) {
            PopularShop::firstOrCreate(['merchant_id' => $id], [
                'display_order' => PopularShop::max('display_order') + 1,
                'created_by'    => auth()->id(),
            ]);
        }

        return response()->json(['message' => 'Popular shops added successfully']);
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $popularShop                    = PopularShop::findOrFail($id);
            $popularShop->is_active         = $request->input('status', 0);
            $popularShop->updated_by        = auth()->id();
            $popularShop->save();

            activity()
                ->useLog('Popular-Shop-Enable-Status-Update')
                ->event('updated')
                ->performedOn($popularShop)
                ->causedBy(auth()->user())
                ->withProperties([
                    'new' => $popularShop->is_active ? 'enabled' : 'disabled',
                    'old' => ! $popularShop->is_active ? 'enabled' : 'disabled',
                ])
                ->log("'$popularShop->shop_name' shop status updated");

            return response()->json(['message' => 'Shop status updated successfully']);
        } catch (\Exception $e) {
            Log::error('PopularShop updateStatus failed', [
                'id'    => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['error' => 'Failed to update shop status '], 500);
        }
    }

    public function updateOrder(PopularShopRequest $request)
    {
        $orderData = $request->validated();

        foreach ($orderData['order'] as $index => $id) {
            PopularShop::where('id', $id)->update(['display_order' => $index + 1]);
        }

        return response()->json(['success' => 'Display Order updated successfully']);
    }

    public function destroy(PopularShop $popularShop)
    {
        $popularShop->delete();

        return response()->json(['message' => 'Removed from popular shops successfully']);
    }
}
