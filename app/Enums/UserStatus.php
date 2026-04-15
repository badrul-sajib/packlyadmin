<?php

namespace App\Enums;

enum UserStatus :int
{
    // 0 = Inactive, 1 = Active, 2 = Deleted 
    case ACTIVE = 1;
    case INACTIVE = 0;
    case DELETED = 2;

    
}
