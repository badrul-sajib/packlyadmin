<?php

namespace App\Enums;

enum ProductAvailabilityStatus: string
{
    case AVAILABLE        = 'Available';
    case PRODUCT_INACTIVE = 'ProductInactive';
    case SHOP_INACTIVE    = 'ShopInactive';
    case STOCK_OUT        = 'StockOut';
    case UNKNOWN          = 'Unknown'; // fallback
}
