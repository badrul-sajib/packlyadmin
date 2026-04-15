<?php

namespace App\Models\Account;

use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Model;

class AccountType extends Model
{
    use HasTimezone;

    protected $connection = 'mysql_internal';

    public static int $ASSET = 1;

    public static int $BANK = 2;

    public static int $INVENTORY = 3;

    public static int $SUPPLIER = 4;

    public static int $LOAN_LIABILITIES = 5;

    public static int $INCOME = 6;

    public static int $PURCHASE = 7;

    public static int $EXPENSE = 8;

    public static int $EQUITY = 9;

    public static int $CASH = 10;
}
