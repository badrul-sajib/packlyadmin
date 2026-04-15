<?php

namespace Modules\Api\V1\Ecommerce\Payment\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Api\V1\Ecommerce\Payment\Http\Resources\PaymentMethodResource;
use App\Models\Payment\PaymentMethod;
use Symfony\Component\HttpFoundation\Response;

class PaymentMethodController extends Controller
{
    public function index()
    {
        $methods = PaymentMethod::active()->get();

        return success('Payment methods fetched successfully', PaymentMethodResource::collection($methods), Response::HTTP_OK);
    }
}
