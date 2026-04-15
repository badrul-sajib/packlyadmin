<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;

class ProductWarranty extends Model
{
    protected $connection = 'mysql_internal';

    protected $fillable = [
        'product_id',
        'new_value',
        'old_value',
        'status',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
