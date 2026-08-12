<?php

namespace App\Domain\Commerce;

enum ReservationProfile: string
{
    case FixedPriceOnlineV1 = 'fixed_price_v1_online';

    public function durationMinutes(): int
    {
        return 15;
    }
}
