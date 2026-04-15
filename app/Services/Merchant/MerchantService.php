<?php

namespace App\Services\Merchant;

use Throwable;
use App\Models\User\User;
use App\Enums\OrderStatus;
use Illuminate\Support\Str;
use App\Models\Help\Message;
use App\Services\SmsService;
use App\Enums\MerchantStatus;
use App\Traits\MerchantTraits;
use App\Models\Merchant\Merchant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use App\Services\UserDefaultDataService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class MerchantService
{
    use MerchantTraits;
    protected UserDefaultDataService $userDefaultDataService;

    public function __construct(UserDefaultDataService $userDefaultDataService)
    {
        $this->userDefaultDataService = $userDefaultDataService;
    }

    public function getAllMerchant($request): LengthAwarePaginator|array
    {
        return $this->merchantList($request);
    }

    public static function getMerchantBySearch($request): Collection|array
    {
        $limit  = $request->input('limit', 20);
        $search = $request->input('search', '');

        return Merchant::query()
            ->when($search, function ($query) use ($search) {
                $query->whereAny(['name', 'shop_name', 'id'], 'like', '%'.$search.'%');
            })
            ->limit($limit)->get();
    }

    public function getMerchantById(string $id): Merchant
    {
        return Merchant::findOrFail($id);
    }

    public function activeMerchant(int $id): RedirectResponse
    {
        $merchant = Merchant::find($id);

        if (! $merchant) {
            return redirect()->back()->with('message', 'Merchant not found');
        }

        $oldStatus = $merchant->shop_status;

        $merchant->update([
            'shop_status' => MerchantStatus::Active->value,
        ]);

        $merchant->shop_products()
            ->with('productHoldStatus')
            ->chunk(100, function (Collection $products) {
                foreach ($products as $product) {
                    if ($product->productHoldStatus) {
                        $product->update([
                            'status' => $product->productHoldStatus->status_id,
                        ]);
                    }
                }
            });

        $causerName = auth()->user()->name;
        $date       = now()->format('d M Y h:i A');

        $message = "Status changed by {$causerName} {$date}";
        $logName = 'merchant-status-update';

        $properties = [
            'new' => MerchantStatus::Active->name,
            'old' => $oldStatus?->name,
        ];

        activity()
            ->useLog($logName)
            ->event('activated')
            ->performedOn($merchant)
            ->causedBy(auth()->user())
            ->withProperties($properties)
            ->log($message);

        try {
            $merchant->sendNotification(
                'Shop Activated',
                'Your Merchant account has been Activated! Please check.'
            );
        } catch (Throwable $th) {
            info($th->getMessage());
        }

        return redirect()->back()->with('message', 'Merchant activated successfully');
    }

    public function resetPassword(Merchant $merchant): RedirectResponse
    {
        $password = collect(range(0, 9))
            ->shuffle()
            ->take(8)
            ->implode('');

        $merchant->user->update([
            'password'        => Hash::make($password),
            'password_expiry' => now()->addMinute(15),
        ]);

        try {
            $smsService  = new SmsService;
            $smsResponse = $smsService->sendMessage(
                $merchant->phone,
                "Your password has been reset. Your new temporary password is: $password Please change your password after logging in. This password will expire in 15 minutes."
            );
        } catch (Throwable $th) {
            info($th->getMessage());
        }

        return redirect()->back()->with('message', 'A temporary password has been sent to the merchants phone number.');
    }

    /**
     * @throws Throwable
     */
    public function createMerchant($request): void
    {
        $password = $request->password ?? Str::random(8);

        DB::transaction(function () use ($request, $password) {
            $slug = Str::slug($request->input('shop_name'));
            do {
                $exists = Merchant::where('slug', $slug)->exists();
                if ($exists) {
                    $increment = rand(1, 2);
                    $slug      = $slug.'-'.str_pad((string) ($increment), 1, '0', STR_PAD_LEFT);
                }
            } while ($exists);

            $user = User::create([
                'name'     => $request->input('name'),
                'phone'    => $request->input('phone'),
                'email'    => $request->input('email'),
                'password' => Hash::make($password),
                'role'     => User::$ROLE_MERCHANT,
            ]);

            $merchant = Merchant::create([
                'user_id'           => $user->id,
                'uuid'              => Merchant::generateUniqueUuid(),
                'name'              => $request->input('name'),
                'phone'             => $request->input('phone'),
                'shop_address'      => $request->input('shop_address'),
                'shop_name'         => $request->input('shop_name'),
                'slug'              => $slug,
                'shop_url'          => $request->input('shop_url'),
                'shop_status'       => (int) $request->input('shop_status'),
                'is_popular_enable' => false,
                'is_verified'       => false,
            ]);

            DB::table('shop_users')->insert([
                'user_id' => $user->id,
                'shop_id' => $merchant->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // add user default data (like accounts)
            $this->userDefaultDataService->insert($merchant);

            try {
                Message::sendMessage($request->input('phone'), "Welcome to Packly.com! Your password is $password".'\n'.'Please change your password after login.');
            } catch (Throwable $th) {
                info($th->getMessage());
            }

            return $merchant;
        });
    }

    public function getCount(int $id): array
    {
        $merchant = Merchant::find($id);

        return [
            'products'             => $merchant->products()->count(),
            'shop_products'        => $merchant->shop_products()->count(),
            'active_shop_products' => $merchant->shop_products()->active()->count(),
            'orders'               => $merchant->orders()->count(),
            'cancel_orders'        => $merchant->orders()->where('status_id', OrderStatus::CANCELLED->value)->count(),
            'customers'            => $merchant->customers()->count(),
        ];
    }

    public function phoneNumberChange($request, $merchant)
    {
        $user        = $merchant->user;
        $user->phone = $request->phone_number;
        $user->save();

        $merchant->phone = $request->phone_number;
        $merchant->save();
    }

    public function emailChange($request, $merchant)
    {
        $user        = $merchant->user;
        $user->email = $request->email;
        $user->save();
    }

    public function getMerchantReports($request, $merchant)
    {
        return $merchant->reports()->with('addedBy')->paginate(10);
    }
}
