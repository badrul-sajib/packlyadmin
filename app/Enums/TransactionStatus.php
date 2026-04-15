<?php

namespace App\Enums;

enum TransactionStatus: int
{
    case PENDING   = 0;
    case SUCCESS   = 1;
    case FAILED    = 2;
    case CANCELLED = 3;

}
