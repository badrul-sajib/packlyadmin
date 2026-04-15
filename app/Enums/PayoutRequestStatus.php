<?php

namespace App\Enums;

enum PayoutRequestStatus: int
{
    case PENDING = 1;
    case READY = 2;
    case APPROVED = 3;
    case HOLDED = 4;

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::READY => 'Ready',
            self::APPROVED => 'Paid',
            self::HOLDED => 'On Hold',
        };
    }

    public static function getLabel(): array
    {
        return [
            self::PENDING->value => 'Pending',
            self::READY->value => 'Ready',
            self::APPROVED->value => 'Paid',
            self::HOLDED->value => 'On Hold',
        ];
    }

    public static function getBgColor(): array
    {
        return [
            self::PENDING->value => 'bg-warning',
            self::READY->value => 'bg-info',
            self::APPROVED->value => 'bg-success',
            self::HOLDED->value => 'bg-danger',
        ];
    }
}
