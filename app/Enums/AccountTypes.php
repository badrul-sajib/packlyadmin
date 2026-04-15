<?php

namespace App\Enums;

enum AccountTypes: int
{
    case ASSET       = 1;
    case BANK        = 2;
    case INVENTORY   = 3;
    case SUPPLIER    = 4;
    case LOAN        = 5;
    case INCOME      = 6;
    case PURCHASE    = 7;
    case EXPENSE     = 8;
    case EQUITY      = 9;
    case CASH        = 10;
    case LOSS        = 11;
    case SALE        = 12;
    case LIABILITIES = 13;
    case MOBILE_BANK = 14;

    public function getValues(): string
    {
        return match ($this) {
            self::ASSET       => 'Asset',
            self::BANK        => 'Bank',
            self::INVENTORY   => 'Inventory',
            self::SUPPLIER    => 'Supplier',
            self::LOAN        => 'Loan',
            self::INCOME      => 'Income',
            self::PURCHASE    => 'Purchase',
            self::EXPENSE     => 'Expense',
            self::EQUITY      => 'Equity',
            self::CASH        => 'Cash',
            self::LOSS        => 'Loss',
            self::SALE        => 'Sale',
            self::LIABILITIES => 'Liabilities',
            self::MOBILE_BANK => 'Mobile Bank',
        };
    }
}
