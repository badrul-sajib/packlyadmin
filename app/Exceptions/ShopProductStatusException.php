<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class ShopProductStatusException extends Exception
{
    public function __construct(
        string $message = 'status update failed',
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
