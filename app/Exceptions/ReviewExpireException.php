<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class ReviewExpireException extends Exception
{
    public function __construct(
        string $message = 'review expire',
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
