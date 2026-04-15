<?php

namespace App\Services\Merchant;

use App\Traits\MerchantTraits;
use App\Actions\FetchMerchantOrders;
use Illuminate\Support\Facades\Auth;

class MerchantKamService
{
    use MerchantTraits;

    public function orders($request)
    {
        $request->merge(['admin_id' => Auth::user()->id]);
        return (new FetchMerchantOrders)->execute($request);
    }
    public function merchants($request)
    {
        $request->merge(['admin_id' => Auth::user()->id]);
        return $this->merchantList($request);
    }
    public function products(): void
    {
       
    }
}
