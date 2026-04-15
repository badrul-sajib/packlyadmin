<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class CategoryCreationException extends Exception
{
    public function __construct(
        string $message = 'Failed to create category',
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
