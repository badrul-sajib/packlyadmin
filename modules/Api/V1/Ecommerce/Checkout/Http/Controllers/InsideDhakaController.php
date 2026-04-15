<?php

namespace Modules\Api\V1\Ecommerce\Checkout\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Api\V1\Ecommerce\Checkout\Http\Requests\InSideDhakaCheckRequest;
use App\Services\InsideDhakaService;

class InsideDhakaController extends Controller
{
    protected InsideDhakaService $insideDhaka;

    public function __construct(InsideDhakaService $insideDhaka)
    {
        $this->insideDhaka = $insideDhaka;
    }

    public function check(InSideDhakaCheckRequest $request)
    {
        // validator make
        $request->validated();

        $address = $request->address;

        // check if string contains bangla characters
        if (preg_match('/[\x80-\xff]/', $address)) {
            $address = banglaToBanglish($address);
        }

        $result = $this->insideDhaka->isInsideDhaka($address);

        return response()->json([
            'inside_dhaka' => $result,
        ]);
    }
}
