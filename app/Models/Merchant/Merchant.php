<?php

namespace App\Models\Merchant;

use App\Enums\MerchantStatus;
use App\Enums\MerchantVerificationStatus;
use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Media\HasMedia;
use App\Media\Mediable;
use App\Models\Account\Expense;
use App\Models\Brand\Brand;
use App\Models\Courier\Courier;
use App\Models\Customer\Customer;
use App\Models\Notification\Notification as ModelsNotification;
use App\Models\PrimeView\PrimeView;
use App\Models\Product\Product;
use App\Models\Purchase\Purchase;
use App\Models\Sell\SellProduct;
use App\Models\Shop\PopularShop;
use App\Models\Shop\ShopProduct;
use App\Models\Shop\ShopSetting;
use App\Models\Stock\StockInventory;
use App\Models\Supplier\Supplier;
use App\Models\User\User;
use App\Models\Warehouse\Warehouse;
use App\Notifications\MerchantNotification;
use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Role;

class Merchant extends Model implements Mediable
{
    use HasMedia, HasTimezone, Notifiable;

    protected $connection = 'mysql_internal';

    protected static array $logAttributes = ['shop_name', 'shop_status', 'user_id'];

    protected $fillable = [
        'uuid',
        'user_id',
        'name',
        'phone',
        'shop_address',
        'shop_name',
        'shop_url',
        'slug',
        'shop_status',
        'is_verified',
        'withdrawal_balance',
        'shop_status_seen',
        'is_popular_enable',
        'auto_approve',
        'payout_hold',
        'balance',
        'admin_id',
        'map_address',
        'latitude',
        'longitude',
        // need these keys for media
        'nid_front_image',
        'nid_back_image',
        'trade_license_images',
        'bank_statement_images',
    ];

    protected $appends = [
        'shop_logo',
        'shop_rating',
        'shop_banner',
        'followers_count',
        'shop_rating_star',
        'rating_count',
    ];

    protected static array $logAttributesToIgnore = [];

    protected static bool $logOnlyDirty = true;

    protected $table;

    public $casts = [
        'created_at' => 'datetime:d-m-Y',
        'shop_status' => MerchantStatus::class,
        'is_verified' => MerchantVerificationStatus::class,
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $database = config('database.connections.mysql_internal.database');

        $this->table = $database.'.'.'merchants';
    }

    // -------------------------- Relationships -------------------#
    public function activities(): MorphMany
    {
        return $this->morphMany(Activity::class, 'subject');
    }

    public function getUserAttribute(): ?User
    {
        return $this->userRelation()->first();
    }

    public function userRelation()
    {
        return $this->users()
            ->where('role', UserRole::MERCHANT->value);
    }

    public function users(): BelongsToMany|Builder
    {
        return $this->belongsToMany(User::class, 'shop_users', 'shop_id', 'user_id')
            ->withTimestamps();
    }

    public function roles(): HasMany|Builder
    {
        return $this->hasMany(Role::class, 'shop_id', 'id');
    }

    public function suppliers(): Merchant|Builder|HasMany
    {
        return $this->hasMany(Supplier::class);
    }

    public function customers(): Merchant|Builder|HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function products(): Merchant|Builder|HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function shop_products(): Merchant|Builder|HasMany
    {
        return $this->hasMany(ShopProduct::class);
    }

    public function orders(): Merchant|Builder|HasMany
    {
        return $this->hasMany(MerchantOrder::class);
    }

    public function warehouses(): Merchant|Builder|HasMany
    {
        return $this->hasMany(Warehouse::class);
    }

    public function expenses(): Merchant|Builder|HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function transactions(): Merchant|Builder|HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function reports(): Merchant|Builder|HasMany
    {
        return $this->hasMany(MerchantReport::class);
    }

    public function brands(): Merchant|Builder|HasMany
    {
        return $this->hasMany(Brand::class);
    }

    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, MerchantFollower::class, 'merchant_id', 'user_id');
    }

    public function couriers(): BelongsToMany
    {
        return $this->belongsToMany(Courier::class, 'courier_merchant')->withPivot('base_url', 'api_key', 'secret_key', 'is_default', 'is_active', 'callback_url', 'auth_token')->withTimestamps();
    }

    public function purchases(): Merchant|Builder|HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    public function sellProducts(): Merchant|Builder|HasMany
    {
        return $this->hasMany(SellProduct::class);
    }

    public function stockInventories(): Merchant|Builder|HasMany
    {
        return $this->hasMany(StockInventory::class);
    }

    public function settings(): Merchant|Builder|HasMany
    {
        return $this->hasMany(MerchantSetting::class);
    }

    public function notifications(): MorphMany
    {
        return $this->morphMany(ModelsNotification::class, 'notifiable')
            ->orderBy('created_at', 'desc');
    }

    public function configuration(): HasOne
    {
        return $this->hasOne(MerchantConfiguration::class, 'merchant_id', 'id');
    }

    public function merchantTransactions(): Merchant|Builder|HasMany
    {
        return $this->hasMany(MerchantTransaction::class);
    }

    public function popularShop(): HasOne
    {
        return $this->hasOne(PopularShop::class, 'merchant_id', 'id');
    }

    public function oldSlugs(): HasMany
    {
        return $this->hasMany(MerchantSlug::class);
    }

    public function productCommissions()
    {
        return $this->hasMany(MerchantProductCommission::class, 'merchant_id', 'id')->orderBy('id', 'asc');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id', 'id');
    }

    // -------------------------- Accessors and Mutators ----------#

    public function getNidFrontImageAttribute()
    {
        return $this->getFirstUrl('nid_front_image');
    }

    public function setNidFrontImageAttribute($value)
    {
        if ($value) {
            $this->deleteMediaCollection('nid_front_image');
            $this->addMedia($value, 'nid_front_image');
        }
    }

    public function getNidBackImageAttribute()
    {
        return $this->getFirstUrl('nid_back_image');
    }

    public function setNidBackImageAttribute($value)
    {
        if ($value) {
            $this->deleteMediaCollection('nid_back_image');
            $this->addMedia($value, 'nid_back_image');
        }
    }

    public function getTradeLicenseImagesAttribute()
    {
        return $this->getMediaIdAndUrl('trade_license_images');
    }

    public function setTradeLicenseImagesAttribute($images)
    {
        if (! empty($images)) {
            foreach ((array) $images as $value) {
                $this->addMedia($value, 'trade_license_images');
            }
        }
    }

    public function getBankStatementImagesAttribute()
    {
        return $this->getMediaIdAndUrl('bank_statement_images');
    }

    public function setBankStatementImagesAttribute($images)
    {
        if (! empty($images)) {
            foreach ((array) $images as $value) {
                $this->addMedia($value, 'bank_statement_images');
            }
        }
    }

    public function getFollowersCountAttribute(): int
    {
        return $this->followers()->count();
    }

    public function getLogoAttribute(): string
    {
        return $this->getFirstUrl('shop_settings', config('app.url'), ['tags' => 'logo']);
    }

    public function getMobileBannerAttribute(): string
    {
        return $this->getFirstUrl('shop_settings', config('app.url'), ['tags' => 'mobile_banner']);
    }

    // -------------------------- Scopes --------------------------#
    public function scopeActive($query)
    {
        return $query->where('shop_status', MerchantStatus::Active);
    }

    // -------------------------- Methods -------------------------#

    public function commission_rate()
    {
        $merchantCommission = $this->hasOne(Commission::class)
            ->whereNull('category_id')
            ->whereNull('product_id')
            ->value('commission_rate');

        if ($merchantCommission !== null) {
            return $merchantCommission;
        }

        if ($this->configuration && $this->configuration->commission_rate) {
            return $this->configuration->commission_rate;
        }

        return ShopSetting::where('key', 'commission_rate')->value('value');
    }

    public function getShopLogoAttribute()
    {
        $setting = MerchantSetting::where('merchant_id', $this->id)
            ->where('key', 'shop_settings')
            ->first();

        if (! $setting || blank($setting->value)) {
            return null;
        }

        $value = json_decode($setting->value, true);

        if (empty($value) || ! is_array($value)) {
            return null;
        }

        return Arr::get($value, 'shop_logo_and_cover.shop_logo.image');
    }

    public function getShopRatingAttribute(): string
    {
        $this->load('products.reviews');
        $reviews = $this->products->pluck('reviews')->flatten();

        if ($reviews->isEmpty()) {
            return '0.00';
        }

        $averageSellerRating = $reviews->avg('seller_rating');
        $averageShippingRating = $reviews->avg('shipping_rating');
        $averageGeneralRating = $reviews->avg('rating');

        $combinedAverage = ($averageSellerRating + $averageShippingRating + $averageGeneralRating) / 3;

        // Convert to percentage (5.0 = 100%, 1.0 = 20%)
        $percentage = ($combinedAverage / 5) * 100;

        return number_format($percentage, 2);
    }

    public function getShopRatingStarAttribute(): float|string
    {
        $this->load('products.reviews');
        $reviews = $this->products->pluck('reviews')->flatten();

        if ($reviews->isEmpty()) {
            return '0.00';
        }

        $averageSellerRating = $reviews->avg('seller_rating');
        $averageShippingRating = $reviews->avg('shipping_rating');
        $averageGeneralRating = $reviews->avg('rating');

        $combinedAverage = ($averageSellerRating + $averageShippingRating + $averageGeneralRating) / 3;

        return number_format($combinedAverage, 2);
    }

    public function getPrimeViewsAttribute(): array
    {
        return PrimeView::where('status', 'active')
            ->whereHas('primeViewProducts', function ($query) {
                return $query->whereHas('product', function ($subQuery) {
                    return $subQuery->where('merchant_id', $this->id)
                        ->where('status', 1);
                });
            })
            ->get()
            ->map(function ($primeView) {
                return [
                    'id' => $primeView->id,
                    'name' => $primeView->name,
                    'slug' => $primeView->slug,
                ];
            })
            ->toArray();
    }

    public function status_label(): string
    {
        return MerchantStatus::label($this->shop_status);
    }

    public function status_color(): string
    {
        return MerchantStatus::color($this->shop_status);
    }

    public static function generateUniqueUuid(): string
    {
        do {
            $uuid = Str::upper(Str::random(8));
        } while (self::where('uuid', $uuid)->exists());

        return $uuid;
    }

    public function returnedOrdersCount(): int
    {
        return MerchantOrder::where('merchant_id', $this->id)
            ->whereHas('orderItems.itemCase', function ($query) {
                $query->where('type', 'return');
            })
            ->distinct()
            ->count();
    }

    public function totalDeliveredOrdersCount(): int
    {
        return MerchantOrder::where('merchant_id', $this->id)
            ->where('status_id', OrderStatus::DELIVERED->value)
            ->count();
    }

    public function getRatingCountAttribute(): int
    {
        $this->loadMissing('products.reviews');

        return $this->products->pluck('reviews')->flatten()->count();
    }

    public function isActive(): bool
    {
        return $this->shop_status->value == MerchantStatus::Active->value;
    }

    public function getShopBannerAttribute()
    {
        $setting = MerchantSetting::where('merchant_id', $this->id)
            ->where('key', 'shop_settings')
            ->first();

        if (! $setting || blank($setting->value)) {
            return null;
        }

        $value = json_decode($setting->value, true);

        if (empty($value) || ! is_array($value)) {
            return null;
        }

        $desktopCover = Arr::get($value, 'shop_logo_and_cover.desktop_cover.image');
        $mobileCover = Arr::get($value, 'shop_logo_and_cover.mobile_cover.image');

        return $desktopCover ?? $mobileCover ?? null;
    }

    public function getDeliveryCharges(): array
    {
        if ($this->configuration && $this->hasDeliveryFeesInConfig()) {
            return [
                'id_delivery_fee' => $this->formatDeliveryFee($this->configuration->id_delivery_fee),
                'od_delivery_fee' => $this->formatDeliveryFee($this->configuration->od_delivery_fee),
                'ed_delivery_fee' => $this->formatDeliveryFee($this->configuration->ed_delivery_fee),
            ];
        }

        return $this->getDeliveryChargesFromShopSettings();
    }

    protected function hasDeliveryFeesInConfig(): bool
    {
        return ! is_null($this->configuration->id_delivery_fee) ||
            ! is_null($this->configuration->ed_delivery_fee) ||
            ! is_null($this->configuration->od_delivery_fee);
    }

    protected function getDeliveryChargesFromShopSettings(): array
    {
        $settings = Cache::remember('delivery_charges_settings', now()->addDays(1), function () {
            return ShopSetting::whereIn('key', ['shipping_fee_isd', 'shipping_fee_osd', 'delivery_charge'])->get();
        });

        $settings = $settings->keyBy('key');

        return [
            'id_delivery_fee' => $this->formatDeliveryFee($settings->get('shipping_fee_isd')->value) ?? null,
            'od_delivery_fee' => $this->formatDeliveryFee($settings->get('shipping_fee_osd')->value) ?? null,
            'ed_delivery_fee' => $this->formatDeliveryFee($settings->get('delivery_charge')->value) ?? null,
        ];
    }

    protected function formatDeliveryFee($value): string
    {
        return sprintf('%.2f', (float) $value);
    }

    public function sendNotification($title, $body, $link = '', $type = 'success'): void
    {
        Notification::send([Merchant::find($this->id)], new MerchantNotification(
            $type,
            [
                'title' => $title,
                'message' => $body,
                'action_url' => $link,
            ],
            $this->userRelation()->first()->id
        ));
    }

    public function getShopSettings(): array
    {
        $defaults = [
            'per_day_request' => 1000,
            'min_amount' => 0,
            'payout_charge' => 0,
            'payout_request_date' => 3,
            'gateway_charge' => 0,
        ];

        // Merchant-level override
        if ($this->configuration) {
            return [
                'per_day_request' => $this->configuration->per_day_request ?? $defaults['per_day_request'],
                'min_amount' => $this->configuration->min_amount ?? $defaults['min_amount'],
                'payout_charge' => $this->configuration->payout_charge ?? $defaults['payout_charge'],
                'payout_request_date' => $this->configuration->payout_request_date ?? $defaults['payout_request_date'],
                'gateway_charge' => $this->configuration->gateway_charge ?? $defaults['gateway_charge'],
            ];
        }

        // Global fallback
        $settings = ShopSetting::whereIn('key', array_keys($defaults))
            ->pluck('value', 'key')
            ->toArray();

        return array_merge($defaults, $settings);
    }

    public function availableBalance(): float
    {
        $shopSettings = $this->getShopSettings();
        $days = (int) $shopSettings['payout_request_date'];

        $merchantOrders = MerchantOrder::where([
            'merchant_id' => $this->id,
            'status_id' => OrderStatus::DELIVERED->value,
            'payout_id' => null,
        ])
            ->where(function ($q) use ($days) {
                $q->where(function ($q) use ($days) {
                    $q->whereNotNull('delivered_at')
                        ->whereDate('delivered_at', '<=', now()->subDays($days));
                })
                    ->orWhere(function ($q) use ($days) {
                        $q->whereNull('delivered_at')
                            ->whereDate('updated_at', '<=', now()->subDays($days));
                    });
            })
            ->with('items') // prevent N+1
            ->get();

        $subtotal = $merchantOrders->sum('sub_total') - $merchantOrders->where('bear_by_packly', '!=', 1)->sum('discount_amount');

        $totalCommission = $merchantOrders->sum(function ($order) {
            return $order->items
                ->where('status_id', OrderStatus::DELIVERED->value)
                ->sum('commission');
        });

        $gatewayCharge = $merchantOrders->sum(
            fn ($order) => $order->gatewayCharge()
        );

        return (float) ($subtotal - $totalCommission - $gatewayCharge);
    }

    // -------------------------- Static --------------------------#
}
