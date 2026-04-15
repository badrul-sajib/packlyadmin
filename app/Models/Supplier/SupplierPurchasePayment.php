<?php

namespace App\Models\Supplier;

use App\Models\Merchant\Merchant;
use App\Models\Purchase\Purchase;
use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Media\HasMedia;
use App\Media\Mediable;

class SupplierPurchasePayment extends Model implements Mediable
{
    use HasMedia, HasTimezone;

    protected $connection = 'mysql_internal';

    protected $guarded = [];

    public static string $FULL_DUE = 'full_due';

    public static string $PARTIAL_PAID = 'partial_paid';

    public static string $FULL_PAID = 'full_paid';

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function supplierPurchasePaymentDetails(): Builder|HasMany|SupplierPurchasePayment
    {
        return $this->hasMany(SupplierPurchasePaymentDetail::class);
    }

    public function supplierPurchasePaymentDetail(): HasOne|Builder|SupplierPurchasePayment
    {
        return $this->hasOne(SupplierPurchasePaymentDetail::class);
    }

    public function setAttachmentAttribute($file): void
    {
        if ($file) {
            $this->addMedia($file, 'attachment', ['tags' => '']);
        }
    }
    public function getAttachmentAttribute(): array
    {
        return $this->getUrl('attachment');
    }
}
