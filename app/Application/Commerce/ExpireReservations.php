<?php

namespace App\Application\Commerce;

use App\Domain\Commerce\OrderState;
use App\Models\Order;

final class ExpireReservations
{
    public function __construct(private readonly ExpireOrderReservation $expire) {}

    public function __invoke(): int
    {
        $expired = 0;

        Order::query()
            ->where('inventory_claim', true)
            ->whereIn('state', [OrderState::Created->value, OrderState::AwaitingPayment->value])
            ->where('reservation_expires_at', '<=', now())
            ->chunkById(100, function ($orders) use (&$expired): void {
                foreach ($orders as $order) {
                    if (($this->expire)($order)->state === OrderState::Expired) {
                        $expired++;
                    }
                }
            });

        return $expired;
    }
}
