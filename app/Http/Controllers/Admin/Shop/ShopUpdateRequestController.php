<?php

namespace App\Http\Controllers\Admin\Shop;

use App\Actions\FetchShopUpdateRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ShopUpdateRequestRequest;
use App\Models\Merchant\Merchant;
use App\Models\Shop\ShopUpdateRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ShopUpdateRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:shop-update-request-list')->only('index');
        $this->middleware('permission:shop-update-request-update')->only(['show', 'update']);
        $this->middleware('permission:shop-update-request-delete')->only('destroy');
    }

    /**
     * @throws Throwable
     */
    public function index(Request $request)
    {
        $shopUpdateRequests = (new FetchShopUpdateRequest)->execute($request);

        if ($request->ajax()) {
            return view('components.shop-update-requests.table', ['data' => $shopUpdateRequests])->render();
        }

        return view('backend.pages.shop_update_requests.index', compact('shopUpdateRequests'));
    }

    public function update(ShopUpdateRequestRequest $request, int $id)
    {
        $validatedStatus = $request->validated()['status'];

        try {
            $shopUpdate = ShopUpdateRequest::findOrFail($id);
            $merchant = $shopUpdate->merchant;

            DB::beginTransaction();
            $shopUpdate->update(['status' => $validatedStatus]);

            $notificationTitle = 'Request Rejected';
            $notificationMessage = 'Your shop information update request has been rejected!';

            if ($validatedStatus === 'approved') {

                if ($merchant->shop_name != $shopUpdate->name) {

                    $oldSlugData = ['slug' => Str::slug($merchant->shop_name)];

                    if ($merchant->oldSlugs()->count() < 5) {
                        $merchant->oldSlugs()->firstOrCreate($oldSlugData);
                    } else {
                        $latestSlug = $merchant->oldSlugs()->latest()->first();

                        if ($latestSlug) {
                            $latestSlug->update($oldSlugData);
                        }
                    }
                }

                $merchant->update([
                    'shop_name' => $shopUpdate->name,
                    'slug' => Str::slug($shopUpdate->name),
                    'shop_address' => $shopUpdate->address,
                    'shop_url' => $shopUpdate->link,
                ]);

                $notificationTitle = 'Request Approved';
                $notificationMessage = 'Your shop information update request has been approved! Please re-login to see the changes.';

                try {
                    Merchant::find($merchant->id)?->sendNotification(
                        $notificationTitle,
                        $notificationMessage,
                    );
                } catch (Throwable $th) {
                    Log::info('Notification send failed for merchant ID: '.$merchant->id);
                }
            }

            DB::commit();

            return response()->json(['message' => "Shop update request {$validatedStatus}!"]);
        } catch (ModelNotFoundException) {
            return response()->json(['message' => 'Shop update request not found!'], Response::HTTP_NOT_FOUND);
        } catch (Throwable $th) {
            DB::rollBack();

            return response()->json(['message' => 'Something went wrong'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(int $id)
    {
        try {
            $shopUpdateRequest = ShopUpdateRequest::findOrFail($id);
            $shopUpdateRequest->delete();

            return response()->json(['message' => 'Shop update request deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return redirect()->back()->with('message', 'Shop update request not found');
        }
    }
}
