<?php

namespace App\Domain\Commerce;

enum ReservationCancellationReason: string
{
    case BuyerCancelled = 'buyer_cancelled';
    case PaymentFailed = 'payment_failed';
    case AdminCorrection = 'admin_correction';
    case ItemUnavailable = 'item_unavailable';
    case Abuse = 'abuse';
    case Support = 'support';

    public function isAdminReason(): bool
    {
        return match ($this) {
            self::AdminCorrection, self::ItemUnavailable, self::Abuse, self::Support => true,
            default => false,
        };
    }
}
