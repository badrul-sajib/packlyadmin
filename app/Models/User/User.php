<?php

namespace App\Models\User;

use Exception;
use App\Enums\UserRole;
use App\Media\HasMedia;
use App\Media\Mediable;
use App\Enums\UserStatus;
use App\Models\Chat\Chat;
use App\Models\Order\Order;
use App\Models\SpamAttempt;
use App\Traits\HasTimezone;
use App\Models\Review\Review;
use App\Models\User\ShopUser;
use App\Models\Chat\Conversation;
use App\Models\Merchant\Merchant;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Coupon\CouponUsage;
use App\Models\Order\CustomerAddress;
use Illuminate\Support\Facades\Cache;
use Illuminate\Notifications\Notifiable;
use App\Models\Merchant\MerchantFollower;
use Illuminate\Database\Eloquent\Builder;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Modules\RolePermission\Traits\RolePermission;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable implements Mediable
{
    use HasApiTokens, HasMedia,  HasTimezone, Notifiable, RolePermission;

    protected string $guard_name = 'admin';

    protected $table;

    protected $guarded = [];

    protected $appends = ['avatar', 'image', 'has_password'];

    protected $hidden = [
        'media',
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'role'              => UserRole::class,
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $database = config('database.connections.mysql_external.database');

        $this->table = $database . '.' . 'users';
    }

    public static int $ROLE_SUPER_ADMIN = 1;

    public static int $ROLE_ADMIN = 2;

    public static int $ROLE_USER = 3;

    public static int $ROLE_MERCHANT = 4;

    public static int $SHOP_ADMIN = 5;

    public static int $MAX_USERS_TO_UPDATE_PERMISSIONS = 50;

    public static array $roles = [
        1 => 'Super Admin',
        2 => 'Admin',
        3 => 'USER',
        4 => 'MERCHANT',
    ];

    public function merchant(): HasOneThrough
    {
        return $this->hasOneThrough(
            Merchant::class,
            ShopUser::class,
            'user_id',
            'id',
            'id',
            'shop_id'
        );
    }

    public function addresses(): Builder|HasMany|User
    {
        return $this->hasMany(CustomerAddress::class, 'user_id', 'id');
    }

    public function orders(): Builder|HasMany|User
    {
        return $this->hasMany(Order::class);
    }

    public function scopeCustomer($query)
    {
        return $query->where('role', self::$ROLE_USER);
    }

    public function getAvatarAttribute(): array|string
    {
        $image        = $this->getFirstUrl('avatar', env('APP_URL'));
        $app_url      = env('APP_URL');
        $app_url_test = env('APP_URL_TEST');

        if ($image && $app_url && $app_url_test) {
            return str_replace($app_url, $app_url_test, $image);
        }

        return $image;
    }

    /**
     * @throws Exception
     */
    public function setAvatarAttribute($file): void
    {
        if ($file) {
            $existingMedia = $this->media()->where('collection_name', 'avatar')->first();
            if ($existingMedia) {
                $this->deleteMedia($existingMedia->id);
            }
            $this->addMedia($file, 'avatar', ['tags' => '']);
        }
    }

    public function reviews(): Builder|HasMany|User
    {
        return $this->hasMany(Review::class);
    }

    public function hasPermission(string $permissionName, ?string $guard = null): bool
    {
        $guardName = $guard ?? $this->getDefaultGuardName();

        $directPermissionQuery = DB::connection('mysql_internal')
            ->table('model_has_permissions')
            ->join('permissions', 'model_has_permissions.permission_id', '=', 'permissions.id')
            ->where('model_has_permissions.model_id', $this->id)
            ->where('model_has_permissions.model_type', get_class($this))
            ->where('model_has_permissions.model_type', self::class)
            ->where('permissions.guard_name', $guardName)
            ->where('permissions.name', $permissionName);

        $hasDirectPermission = (clone $directPermissionQuery)->exists();

        if ($hasDirectPermission) {
            return true;
        }

        $hasAnyDirectPermission = DB::connection('mysql_internal')
            ->table('model_has_permissions')
            ->join('permissions', 'model_has_permissions.permission_id', '=', 'permissions.id')
            ->where('model_has_permissions.model_id', $this->id)
            ->where('model_has_permissions.model_type', self::class)
            ->where('permissions.guard_name', $guardName)
            ->exists();

        // User-wise override mode:
        // If any direct permission exists for this user, only direct permissions are respected.
        // Role permissions are used only when user has no direct permission mapping.
        if ($hasAnyDirectPermission) {
            return false;
        }

        $roleIds = DB::connection('mysql_internal')
            ->table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('model_has_roles.model_id', $this->id)
            ->where('model_has_roles.model_type', self::class)
            ->where('roles.status', 1)
            ->where('roles.guard_name', $guardName)
            ->pluck('roles.id')
            ->toArray();

        if (empty($roleIds)) {
            return false;
        }

        return DB::connection('mysql_internal')
            ->table('role_has_permissions')
            ->join('permissions', 'role_has_permissions.permission_id', '=', 'permissions.id')
            ->join('roles', 'role_has_permissions.role_id', '=', 'roles.id')
            ->whereIn('role_has_permissions.role_id', $roleIds)
            ->where('roles.guard_name', $guardName)
            ->where('permissions.guard_name', $guardName)
            ->where('permissions.name', $permissionName)
            ->exists();
    }

    protected function getDefaultGuardName(): string
    {
        return $this->attributes['guard_name'] ?? $this->guard_name;
    }


    public function hasRole(string $roleName): bool
    {
        return DB::connection('mysql_internal')
            ->table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('model_has_roles.model_id', $this->id)
            ->where('model_has_roles.model_type', get_class($this))
            ->where('roles.status', 1)
            ->where('roles.name', $roleName)
            ->exists();
    }


    public function couponUsages(): Builder|HasMany|User
    {
        return $this->hasMany(CouponUsage::class, 'user_id', 'id');
    }

    public function scopeIsAdmin($query)
    {
        return $query->where('role', UserRole::SUPER_ADMIN);
    }

    public function routeNotificationForBroadcast(): string
    {
        return 'User.' . $this->id;
    }

    public function conversations(): BelongsToMany
    {
        return $this->belongsToMany(Conversation::class, 'conversation_users');
    }

    public function chats(): Builder|HasMany|User
    {
        return $this->hasMany(Chat::class);
    }

    public function socketTokens(): Builder|HasMany|User
    {
        return $this->hasMany(SocketToken::class);
    }

    public function followedMerchants(): BelongsToMany
    {
        $pivotTable = config('database.connections.mysql_internal.database') . '.merchant_follower';

        return $this->belongsToMany(Merchant::class, $pivotTable, 'user_id', 'merchant_id')
            ->using(MerchantFollower::class);
    }

    /**
     * @throws Exception
     */
    public function setImageAttribute($file): void
    {
        if ($file) {
            $this->deleteMedia();

            $this->addMedia($file, 'images', [
                'tags' => 'profile',
            ]);
        }
    }

    public function getImageAttribute(): string
    {
        return $this->getFirstUrl('images');
    }

    public function routeNotificationFor($driver, $notification = null)
    {
        if ($driver === 'database') {
            return $this;
        }

        return $this->id;
    }

    public function kamMerchants()
    {
        return $this->belongsToMany(Merchant::class, 'admin_merchants', 'admin_id', 'merchant_id');
    }


    public function spamAttempts(): HasMany
    {
        return $this->hasMany(SpamAttempt::class);
    }
    public function getHasPasswordAttribute()
    {
        return !is_null($this->password);
    }
}
