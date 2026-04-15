<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class InsufficientException extends Exception
{
    public function __construct(
        string $message = 'insufficient value',
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
