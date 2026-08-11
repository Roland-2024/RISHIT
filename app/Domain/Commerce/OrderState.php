<?php

namespace App\Domain\Commerce;

use DomainException;

enum OrderState: string
{
    case Created = 'created';
    case AwaitingPayment = 'awaiting_payment';
    case Paid = 'paid';
    case AwaitingShipment = 'awaiting_shipment';
    case Shipped = 'shipped';
    case Delivered = 'delivered';
    case Completed = 'completed';

    public function assertCanTransitionTo(self $next): void
    {
        $allowed = match ($this) {
            self::Created => self::AwaitingPayment,
            self::AwaitingPayment => self::Paid,
            self::Paid => self::AwaitingShipment,
            self::AwaitingShipment => self::Shipped,
            self::Shipped => self::Delivered,
            self::Delivered => self::Completed,
            self::Completed => null,
        };

        if ($next !== $allowed) {
            throw new DomainException("Invalid order transition from {$this->value} to {$next->value}.");
        }
    }
}
