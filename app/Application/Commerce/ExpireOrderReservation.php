<?php

namespace App\Application\Commerce;

use App\Domain\Catalog\ListingStatus;
use App\Domain\Commerce\OrderState;
use App\Models\Listing;
use App\Models\Order;
use DomainException;
use Illuminate\Support\Facades\DB;

final class ExpireOrderReservation
{
    public function __construct(private readonly ReleaseOrderReservation $release) {}

    public function __invoke(Order $order): Order
    {
        return DB::transaction(function () use ($order): Order {
            $order = Order::query()->lockForUpdate()->findOrFail($order->getKey());

            if ($order->state === OrderState::Expired || ! $order->state->isAwaitingPayment()) {
                return $order;
            }

            if (! $order->inventory_claim || $order->reservation_expires_at === null) {
                throw new DomainException('The order has no active expiring reservation.');
            }

            if ($order->reservation_expires_at->isFuture()) {
                throw new DomainException('The reservation deadline has not passed.');
            }

            $listing = Listing::query()->withTrashed()->lockForUpdate()->findOrFail($order->listing_id);
            ($this->release)(
                $order,
                $listing,
                OrderState::Expired,
                null,
                'reservation_expired',
                ListingStatus::Active,
            );

            return $order->refresh();
        }, 3);
    }
}
