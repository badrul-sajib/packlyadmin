<?php

namespace App\Services\Checkout\Validators;

use App\Models\Merchant\Merchant;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DeliveryTypeValidator
{
    public function validate($deliveryType): void
    {
        $merchantIds = array_keys($deliveryType);
        $merchants   = Merchant::whereIn('id', $merchantIds)->count();

        if ($merchants !== count($merchantIds)) {
            throw new ModelNotFoundException('Merchant not found');
        }
    }
}
