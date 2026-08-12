<?php

namespace App\Application\Commerce;

use App\Domain\Commerce\OrderState;
use App\Models\Order;
use App\Models\OrderTransition;
use App\Models\User;
use DomainException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

final class TransitionOrder
{
    public function __invoke(Order $order, OrderState $next, ?User $actor = null): Order
    {
        return DB::transaction(function () use ($order, $next, $actor): Order {
            $order = Order::query()->lockForUpdate()->findOrFail($order->getKey());

            if ($actor) {
                Gate::forUser($actor)->authorize('view', $order);
            }

            if (in_array($next, [OrderState::Cancelled, OrderState::Expired], true)) {
                throw new DomainException('Reservation cancellation and expiry require their dedicated actions.');
            }

            if ($next === OrderState::Paid && $actor !== null) {
                throw new DomainException('A participant cannot confirm payment.');
            }

            $order->state->assertCanTransitionTo($next);
            $from = $order->state;
            $now = now();

            $order->forceFill([
                'state' => $next,
                'state_changed_at' => $now,
            ])->save();

            $transition = new OrderTransition;
            $transition->forceFill([
                'order_id' => $order->getKey(),
                'actor_id' => $actor?->getKey(),
                'from_state' => $from,
                'to_state' => $next,
                'occurred_at' => $now,
            ])->save();

            return $order->refresh();
        }, 3);
    }
}
