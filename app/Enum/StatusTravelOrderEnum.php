<?php

namespace App\Enum;

enum StatusTravelOrderEnum: int
{
    case PENDING = 1;
    case ACCEPTED = 2;
    case CANCELED = 3;

    public static function getNameById(int $id): string
    {
        return match ($id) {
            self::PENDING->value => 'pending',
            self::ACCEPTED->value => 'accepted',
            self::CANCELED->value => 'canceled',
        };
    }
}
