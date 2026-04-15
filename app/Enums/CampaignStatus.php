<?php

namespace App\Enums;

enum CampaignStatus: int
{
    case DRAFT = 1;
    case OPEN_FOR_PRIME_VIEW_REQUEST = 2;
    case CLOSED_FOR_PRIME_VIEW_REQUEST = 3;
    case PUBLISHED = 4;
    case UNPUBLISHED = 5;

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::OPEN_FOR_PRIME_VIEW_REQUEST => 'Open for Prime View Request',
            self::CLOSED_FOR_PRIME_VIEW_REQUEST => 'Closed for Prime View Request',
            self::PUBLISHED => 'Published',
            self::UNPUBLISHED => 'Unpublished',
        };
    }
}
