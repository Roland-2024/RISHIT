<?php

namespace App\Application\Commerce;

use App\Domain\Catalog\ListingStatus;
use App\Domain\Commerce\BuyerFeePolicy;
use App\Domain\Commerce\OrderAmounts;
use App\Domain\Commerce\OrderState;
use App\Domain\Commerce\ReservationProfile;
use App\Domain\Shared\Money;
use App\Models\Address;
use App\Models\Listing;
use App\Models\Order;
use App\Models\OrderTransition;
use App\Models\User;
use DomainException;
use Illuminate\Support\Facades\DB;

final class CreateOrder
{
    public function __invoke(
        Listing $listing,
        User $buyer,
        Address $buyerAddress,
        Address $sellerAddress,
        Money $authoritativeShipping,
        string $idempotencyKey,
    ): Order {
        if (trim($idempotencyKey) === '' || strlen($idempotencyKey) > 64) {
            throw new DomainException('An idempotency key of at most 64 characters is required.');
        }

        return DB::transaction(function () use ($listing, $buyer, $buyerAddress, $sellerAddress, $authoritativeShipping, $idempotencyKey): Order {
            $buyer = User::query()->lockForUpdate()->findOrFail($buyer->getKey());

            $existing = Order::query()
                ->where('buyer_id', $buyer->getKey())
                ->where('idempotency_key', $idempotencyKey)
                ->lockForUpdate()
                ->first();

            if ($existing) {
                return $this->retry($existing, $listing, $buyerAddress, $sellerAddress, $authoritativeShipping);
            }

            $listing = Listing::query()
                ->withTrashed()
                ->with(['brand', 'category'])
                ->lockForUpdate()
                ->findOrFail($listing->getKey());
            $seller = User::query()->lockForUpdate()->findOrFail($listing->user_id);
            $buyerAddress = Address::query()->lockForUpdate()->findOrFail($buyerAddress->getKey());
            $sellerAddress = Address::query()->lockForUpdate()->findOrFail($sellerAddress->getKey());

            if (! $listing->isPubliclyVisible()) {
                throw new DomainException('Only active EUR listings can become orders.');
            }

            if ($buyer->is($seller)) {
                throw new DomainException('A seller cannot buy their own listing.');
            }

            if ($buyerAddress->user_id !== $buyer->getKey() || $sellerAddress->user_id !== $seller->getKey()) {
                throw new DomainException('Order addresses must belong to the buyer and seller.');
            }

            $policy = BuyerFeePolicy::from(config('marketplace.buyer_fee_policy'));
            $amounts = OrderAmounts::calculate($listing->price(), $authoritativeShipping, $policy);
            $profile = ReservationProfile::FixedPriceOnlineV1;
            $now = now()->toImmutable();

            $order = new Order;
            $order->forceFill([
                'listing_id' => $listing->getKey(),
                'buyer_id' => $buyer->getKey(),
                'seller_id' => $seller->getKey(),
                'idempotency_key' => $idempotencyKey,
                'state' => OrderState::Created,
                'reservation_profile' => $profile,
                'reservation_started_at' => $now,
                'reservation_expires_at' => $now->addMinutes($profile->durationMinutes()),
                'inventory_claim' => true,
                'currency' => $amounts->currency,
                'item_amount' => $amounts->item,
                'shipping_amount' => $amounts->shipping,
                'buyer_fee_amount' => $amounts->buyerFee,
                'seller_fee_amount' => $amounts->sellerFees(),
                'total_amount' => $amounts->total,
                'seller_payable_amount' => $amounts->sellerPayable,
                'buyer_fee_policy_version' => $policy,
                'fee_policy_snapshot' => $amounts->snapshot($policy),
                'item_snapshot' => $this->itemSnapshot($listing),
                'buyer_snapshot' => $this->userSnapshot($buyer),
                'seller_snapshot' => $this->userSnapshot($seller),
                'buyer_address_snapshot' => $this->addressSnapshot($buyerAddress),
                'seller_address_snapshot' => $this->addressSnapshot($sellerAddress),
                'state_changed_at' => $now,
            ])->save();

            $transition = new OrderTransition;
            $transition->forceFill([
                'order_id' => $order->getKey(),
                'actor_id' => $buyer->getKey(),
                'from_state' => null,
                'to_state' => OrderState::Created,
                'reason' => 'reservation_created',
                'listing_status' => ListingStatus::Reserved->value,
                'occurred_at' => $now,
            ])->save();

            $listing->forceFill(['status' => ListingStatus::Reserved])->save();

            return $order->refresh();
        }, 3);
    }

    private function retry(
        Order $order,
        Listing $listing,
        Address $buyerAddress,
        Address $sellerAddress,
        Money $shipping,
    ): Order {
        if (
            $order->listing_id !== $listing->getKey()
            || $order->buyer_address_snapshot['id'] !== $buyerAddress->getKey()
            || $order->seller_address_snapshot['id'] !== $sellerAddress->getKey()
            || $order->shipping_amount !== $shipping->amount
            || $order->currency !== $shipping->currency
        ) {
            throw new DomainException('The idempotency key was already used for different order inputs.');
        }

        if (! $order->inventory_claim || ($order->state->isAwaitingPayment() && $order->reservation_expires_at->lessThanOrEqualTo(now()))) {
            throw new DomainException('The idempotency key belongs to a stale reservation.');
        }

        return $order->refresh();
    }

    /** @return array<string, mixed> */
    private function itemSnapshot(Listing $listing): array
    {
        return [
            'id' => $listing->getKey(),
            'slug' => $listing->slug,
            'title' => $listing->title,
            'description' => $listing->description,
            'condition' => $listing->condition->value,
            'size' => $listing->size,
            'color' => $listing->color,
            'category' => $listing->category ? [
                'id' => $listing->category->getKey(),
                'name_sq' => $listing->category->name_sq,
                'name_en' => $listing->category->name_en,
            ] : null,
            'brand' => $listing->brand ? [
                'id' => $listing->brand->getKey(),
                'name' => $listing->brand->name,
            ] : null,
        ];
    }

    /** @return array<string, int|string> */
    private function userSnapshot(User $user): array
    {
        return [
            'id' => $user->getKey(),
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
        ];
    }

    /** @return array<string, int|string> */
    private function addressSnapshot(Address $address): array
    {
        return [
            'id' => $address->getKey(),
            'label' => $address->label,
            'recipient_name' => $address->recipient_name,
            'phone' => $address->phone,
            'street' => $address->street,
            'city' => $address->city,
            'postal_code' => $address->postal_code,
            'country_code' => $address->country_code,
        ];
    }
}
