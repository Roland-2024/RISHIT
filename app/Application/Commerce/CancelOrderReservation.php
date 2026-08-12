<?php

namespace App\Application\Commerce;

use App\Domain\Catalog\ListingStatus;
use App\Domain\Commerce\OrderState;
use App\Domain\Commerce\ReservationCancellationReason;
use App\Models\Listing;
use App\Models\Order;
use App\Models\User;
use DomainException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;

final class CancelOrderReservation
{
    public function __construct(private readonly ReleaseOrderReservation $release) {}

    public function __invoke(
        Order $order,
        ?User $actor,
        ReservationCancellationReason $reason,
        ?ListingStatus $adminListingOutcome = null,
    ): Order {
        return DB::transaction(function () use ($order, $actor, $reason, $adminListingOutcome): Order {
            $order = Order::query()->lockForUpdate()->findOrFail($order->getKey());
            $actor = $actor ? User::query()->findOrFail($actor->getKey()) : null;
            $listingOutcome = $this->listingOutcome($order, $actor, $reason, $adminListingOutcome);

            if ($order->state === OrderState::Cancelled) {
                return $order;
            }

            if (! $order->state->isAwaitingPayment() || ! $order->inventory_claim) {
                throw new DomainException('Only an active pre-payment reservation can be cancelled.');
            }

            if ($order->reservation_expires_at?->lessThanOrEqualTo(now())) {
                throw new DomainException('The reservation deadline passed and must be expired.');
            }

            $listing = Listing::query()->withTrashed()->lockForUpdate()->findOrFail($order->listing_id);
            ($this->release)($order, $listing, OrderState::Cancelled, $actor, $reason->value, $listingOutcome);

            return $order->refresh();
        }, 3);
    }

    private function listingOutcome(
        Order $order,
        ?User $actor,
        ReservationCancellationReason $reason,
        ?ListingStatus $adminListingOutcome,
    ): ListingStatus {
        if ($actor === null) {
            if ($reason !== ReservationCancellationReason::PaymentFailed || $adminListingOutcome !== null) {
                throw new AuthorizationException('System cancellation is limited to definitive payment failure.');
            }

            return ListingStatus::Active;
        }

        if ($actor->getKey() === $order->buyer_id) {
            if ($reason !== ReservationCancellationReason::BuyerCancelled || $adminListingOutcome !== null) {
                throw new AuthorizationException('Buyers may only cancel their own reservation.');
            }

            return ListingStatus::Active;
        }

        if (! $actor->is_admin || ! $reason->isAdminReason()) {
            throw new AuthorizationException('Only the buyer, system, or an administrator may cancel a reservation.');
        }

        if (! in_array($adminListingOutcome, [ListingStatus::Active, ListingStatus::Hidden], true)) {
            throw new DomainException('An administrator must choose whether to reactivate or hide the listing.');
        }

        if ($reason === ReservationCancellationReason::ItemUnavailable && $adminListingOutcome !== ListingStatus::Hidden) {
            throw new DomainException('An unavailable item must remain hidden.');
        }

        return $adminListingOutcome;
    }
}
