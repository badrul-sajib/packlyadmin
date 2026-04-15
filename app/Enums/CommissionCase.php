<?php

namespace App\Enums;

enum CommissionCase: int
{
    case DATE_RANGE_WITH_ALL_FILTERS       = 1;          // Case 1 category, product and merchant && date
    case DATE_RANGE_WITH_CATEGORY_PRODUCT  = 2;     // Case 2
    case DATE_RANGE_WITH_CATEGORY_MERCHANT = 3;    // Case 3
    case DATE_RANGE_WITH_PRODUCT_MERCHANT  = 4;     // Case 4
    case DATE_RANGE_ONLY                   = 5;                      // Case 5

    case ALL_FILTERS       = 6;                          // Case 6 category, product and merchant
    case CATEGORY_PRODUCT  = 7;                     // Case 7
    case CATEGORY_MERCHANT = 8;                    // Case 8
    case PRODUCT_MERCHANT  = 9;                     // Case 9
    case PRODUCT_ONLY      = 10;                        // Case 10
    case CATEGORY_ONLY     = 11;                       // Case 11
    case MERCHANT_ONLY     = 12;
}
