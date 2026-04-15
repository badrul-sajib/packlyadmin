<?php

namespace App\Models\Merchant;

use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Model;

class MerchantProductCommission extends Model
{
    protected $table;

    protected $fillable = [
        'merchant_id',
        'product_id',
        'commission_rate',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $database = config('database.connections.mysql_internal.database');
        $this->table = $database.'.'.'merchant_product_commissions';
    }


    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }


}
