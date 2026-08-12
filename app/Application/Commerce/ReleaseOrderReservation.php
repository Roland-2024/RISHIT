<?php

namespace App\Application\Commerce;

use App\Domain\Catalog\ListingStatus;
use App\Domain\Commerce\OrderState;
use App\Models\Listing;
use App\Models\Order;
use App\Models\OrderTransition;
use App\Models\User;
use LogicException;

final class ReleaseOrderReservation
{
    public function __invoke(
        Order $order,
        Listing $listing,
        OrderState $terminalState,
        ?User $actor,
        string $reason,
        ListingStatus $requestedListingStatus,
    ): void {
        if (! in_array($terminalState, [OrderState::Cancelled, OrderState::Expired], true)) {
            throw new LogicException('A reservation can only be released as cancelled or expired.');
        }

        if (! in_array($requestedListingStatus, [ListingStatus::Active, ListingStatus::Hidden], true)) {
            throw new LogicException('A released reservation must reactivate or hide its listing.');
        }

        $order->state->assertCanTransitionTo($terminalState);
        $from = $order->state;
        $now = now();

        if (! $listing->trashed()) {
            if ($requestedListingStatus === ListingStatus::Hidden) {
                $listing->forceFill(['status' => ListingStatus::Hidden])->save();
            } elseif ($listing->canReactivateAfterReservation()) {
                $listing->forceFill(['status' => ListingStatus::Active])->save();
            } elseif ($listing->status === ListingStatus::Reserved) {
                $listing->forceFill(['status' => ListingStatus::Hidden])->save();
            }
        }

        $order->forceFill([
            'state' => $terminalState,
            'inventory_claim' => null,
            'state_changed_at' => $now,
        ])->save();

        $transition = new OrderTransition;
        $transition->forceFill([
            'order_id' => $order->getKey(),
            'actor_id' => $actor?->getKey(),
            'from_state' => $from,
            'to_state' => $terminalState,
            'reason' => $reason,
            'listing_status' => $listing->trashed() ? 'deleted' : $listing->status->value,
            'occurred_at' => $now,
        ])->save();
    }
}
