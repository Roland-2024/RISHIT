<?php

namespace App\Domain\Commerce;

use DomainException;

enum OrderState: string
{
    case Created = 'created';
    case AwaitingPayment = 'awaiting_payment';
    case Cancelled = 'cancelled';
    case Expired = 'expired';
    case Paid = 'paid';
    case AwaitingShipment = 'awaiting_shipment';
    case Shipped = 'shipped';
    case Delivered = 'delivered';
    case Completed = 'completed';

    public function assertCanTransitionTo(self $next): void
    {
        $allowed = match ($this) {
            self::Created => [self::AwaitingPayment, self::Cancelled, self::Expired],
            self::AwaitingPayment => [self::Paid, self::Cancelled, self::Expired],
            self::Paid => [self::AwaitingShipment],
            self::AwaitingShipment => [self::Shipped],
            self::Shipped => [self::Delivered],
            self::Delivered => [self::Completed],
            self::Cancelled, self::Expired, self::Completed => [],
        };

        if (! in_array($next, $allowed, true)) {
            throw new DomainException("Invalid order transition from {$this->value} to {$next->value}.");
        }
    }

    public function isAwaitingPayment(): bool
    {
        return $this === self::Created || $this === self::AwaitingPayment;
    }
}
