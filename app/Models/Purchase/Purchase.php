<?php

namespace App\Models\Purchase;

use App\Media\HasMedia;
use App\Media\Mediable;
use App\Models\Merchant\Merchant;
use App\Models\Payment\Payment;
use App\Models\Stock\StockInventory;
use App\Models\Supplier\Supplier;
use App\Models\Supplier\SupplierPurchasePayment;
use App\Models\Warehouse\Warehouse;
use App\Traits\HasTimezone;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Purchase extends Model implements Mediable
{
    use HasMedia, HasTimezone;

    protected $connection = 'mysql_internal';

    protected $guarded = [];

    public static int $PURCHASE_TYPE_ORDERED = 1;

    public static int $PURCHASE_TYPE_Unordered = 2;

    public static int $PAYMENT_STATUS_DUE = 1;

    public static int $PAYMENT_STATUS_PAID = 2;

    public static int $PAYMENT_STATUS_PARTIAL = 3;

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function purchaseDetails(): Purchase|Builder|HasMany
    {
        return $this->hasMany(PurchaseDetail::class);
    }

    /**
     * @throws Exception
     */
    public function setImageAttribute($file): void
    {
        if ($file) {
            $this->addMedia($file, 'attachment', ['tags' => '']);
        }
    }

    /**
     * @throws Exception
     */
    public function getImageAttribute(): array
    {
        return $this->getUrl('attachment');
    }

    public function payments(): Purchase|Builder|HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function stockInventory(): Purchase|Builder|HasMany
    {
        return $this->hasMany(StockInventory::class);
    }

    public function supplierPurchasePayment(): HasOne|Purchase|Builder
    {
        return $this->hasOne(SupplierPurchasePayment::class);
    }

    public function supplierPurchasePayments(): Purchase|Builder|HasMany
    {
        return $this->hasMany(SupplierPurchasePayment::class);
    }
}
