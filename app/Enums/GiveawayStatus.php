<?php
namespace App\Enums;

enum GiveawayStatus: string
{
    case Scheduled = 'scheduled';
    case Active = 'active';
    case Ended = 'ended';
    case Drawn = 'drawn';
    case Cancelled = 'cancelled';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::Scheduled => 'Scheduled',
            self::Active => 'Active',
            self::Ended => 'Ended',
            self::Drawn => 'Drawn',
            self::Cancelled => 'Cancelled',
        };
    }

    public function canTransitionTo(self $to): bool
    {
        return match ($this) {
            self::Scheduled => in_array($to, [self::Scheduled, self::Active, self::Cancelled], true),
            self::Active => in_array($to, [self::Active, self::Scheduled,self::Ended, self::Cancelled], true),
            self::Ended => in_array($to, [self::Ended, self::Cancelled], true),
            self::Drawn => $to === self::Drawn,
            self::Cancelled => $to === self::Cancelled,
        };
    }
}

