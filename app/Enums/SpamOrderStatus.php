<?php

namespace App\Enums;

enum SpamOrderStatus: string
{
    case ALLOW = 'ALLOW';
    case SOFT_CHALLENGE = 'SOFT_CHALLENGE';
    case HARD_DECLINE = 'HARD_DECLINE';
    case HOLD_FOR_REVIEW = 'HOLD_FOR_REVIEW';

    public function color(): string
    {
        return match ($this) {
            SpamOrderStatus::ALLOW => 'badge bg-success',
            SpamOrderStatus::SOFT_CHALLENGE => 'badge bg-warning',
            SpamOrderStatus::HARD_DECLINE => 'badge bg-danger',
            SpamOrderStatus::HOLD_FOR_REVIEW => 'badge bg-info',
        };
    }
}
