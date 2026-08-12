<?php

namespace Tests\Feature;

use App\Application\Commerce\CancelOrderReservation;
use App\Application\Commerce\CreateOrder;
use App\Application\Commerce\ExpireOrderReservation;
use App\Application\Commerce\TransitionOrder;
use App\Domain\Catalog\ListingStatus;
use App\Domain\Commerce\BuyerFeePolicy;
use App\Domain\Commerce\OrderState;
use App\Domain\Commerce\ReservationCancellationReason;
use App\Domain\Shared\Currency;
use App\Domain\Shared\Money;
use App\Models\Address;
use App\Models\Listing;
use App\Models\OrderTransition;
use App\Models\User;
use DomainException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use LogicException;
use Tests\TestCase;

class OrderDomainTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_creation_uses_server_sources_and_keeps_immutable_snapshots(): void
    {
        [$listing, $buyer, $buyerAddress, $sellerAddress] = $this->orderInputs();

        $listing->forceFill(['price_amount' => 1]);
        $buyer->forceFill(['name' => 'Unpersisted buyer']);
        $buyerAddress->forceFill(['street' => 'Unpersisted street']);

        $order = app(CreateOrder::class)(
            $listing,
            $buyer,
            $buyerAddress,
            $sellerAddress,
            new Money(750, Currency::EUR),
            'create-snapshot-order',
        );

        $this->assertSame(OrderState::Created, $order->state);
        $this->assertSame(Currency::EUR, $order->currency);
        $this->assertSame(10_000, $order->item_amount);
        $this->assertSame(750, $order->shipping_amount);
        $this->assertSame(0, $order->buyer_fee_amount);
        $this->assertSame(0, $order->seller_fee_amount);
        $this->assertSame(10_750, $order->total_amount);
        $this->assertSame(10_000, $order->seller_payable_amount);
        $this->assertSame(BuyerFeePolicy::NoFeeV1, $order->buyer_fee_policy_version);
        $this->assertSame(0, $order->fee_policy_snapshot['buyer']['tax_amount']);
        $this->assertSame(0, $order->fee_policy_snapshot['seller_listing_fee_amount']);
        $this->assertSame(0, $order->fee_policy_snapshot['seller_selling_fee_amount']);
        $this->assertSame(ListingStatus::Reserved, $listing->fresh()->status);
        $this->assertSame('fixed_price_v1_online', $order->reservation_profile->value);
        $this->assertTrue($order->inventory_claim);
        $this->assertTrue($order->reservation_expires_at->equalTo($order->reservation_started_at->addMinutes(15)));
        $this->assertDatabaseHas('order_transitions', [
            'order_id' => $order->id,
            'from_state' => null,
            'to_state' => OrderState::Created->value,
            'actor_id' => $buyer->id,
        ]);

        $listing->refresh()->forceFill(['title' => 'Changed title', 'price_amount' => 99_999])->save();
        $buyer->refresh()->forceFill(['name' => 'Changed buyer'])->save();
        $buyerAddress->refresh()->forceFill(['street' => 'Changed street'])->save();
        $sellerAddress->forceFill(['city' => 'Changed city'])->save();
        $order->refresh();

        $this->assertSame('Snapshot coat', $order->item_snapshot['title']);
        $this->assertSame('Buyer Before', $order->buyer_snapshot['name']);
        $this->assertSame('Buyer Street 1', $order->buyer_address_snapshot['street']);
        $this->assertSame('Tirana', $order->seller_address_snapshot['city']);
        $this->assertSame(10_000, $order->item_amount);
    }

    public function test_commercial_snapshots_cannot_be_changed(): void
    {
        [$listing, $buyer, $buyerAddress, $sellerAddress] = $this->orderInputs();
        $order = app(CreateOrder::class)(
            $listing,
            $buyer,
            $buyerAddress,
            $sellerAddress,
            new Money(0, Currency::EUR),
            'immutable-order',
        );

        try {
            $order->forceFill(['total_amount' => 1])->save();
            $this->fail('Changing an order total should fail.');
        } catch (LogicException $exception) {
            $this->assertSame('Order commercial snapshots cannot be changed.', $exception->getMessage());
        }

        $this->assertSame(10_000, $order->fresh()->total_amount);
    }

    public function test_invalid_state_transitions_are_rejected_and_valid_ones_are_audited(): void
    {
        [$listing, $buyer, $buyerAddress, $sellerAddress] = $this->orderInputs();
        $order = app(CreateOrder::class)(
            $listing,
            $buyer,
            $buyerAddress,
            $sellerAddress,
            new Money(0, Currency::EUR),
            'transition-order',
        );

        try {
            app(TransitionOrder::class)($order, OrderState::Paid, $buyer);
            $this->fail('Skipping an order state should fail.');
        } catch (DomainException $exception) {
            $this->assertStringContainsString('cannot confirm payment', $exception->getMessage());
        }

        $this->assertSame(OrderState::Created, $order->fresh()->state);
        $this->assertCount(1, $order->transitions()->get());

        $order = app(TransitionOrder::class)($order, OrderState::AwaitingPayment, $buyer);

        $this->assertSame(OrderState::AwaitingPayment, $order->state);
        $this->assertDatabaseHas('order_transitions', [
            'order_id' => $order->id,
            'from_state' => OrderState::Created->value,
            'to_state' => OrderState::AwaitingPayment->value,
            'actor_id' => $buyer->id,
        ]);
    }

    public function test_unique_listing_and_party_address_rules_are_enforced(): void
    {
        [$listing, $buyer, $buyerAddress, $sellerAddress] = $this->orderInputs();
        $create = app(CreateOrder::class);

        $create($listing, $buyer, $buyerAddress, $sellerAddress, new Money(0, Currency::EUR), 'first-buyer');
        $listing->forceFill(['status' => ListingStatus::Active])->save();
        $this->assertFalse($listing->fresh()->isPubliclyVisible());
        $this->assertFalse(Listing::query()->visible()->whereKey($listing->id)->exists());
        $otherBuyer = User::factory()->create();
        $otherAddress = $this->addressFor($otherBuyer, 'Other buyer');

        $this->expectException(DomainException::class);
        $create($listing, $otherBuyer, $otherAddress, $sellerAddress, new Money(0, Currency::EUR), 'second-buyer');
    }

    public function test_creation_retries_are_idempotent_without_extending_or_reusing_a_stale_key(): void
    {
        $this->travelTo('2026-08-12 10:00:00');
        [$listing, $buyer, $buyerAddress, $sellerAddress] = $this->orderInputs();
        $create = app(CreateOrder::class);

        $order = $create($listing, $buyer, $buyerAddress, $sellerAddress, new Money(0, Currency::EUR), 'same-request');
        $deadline = $order->reservation_expires_at;

        $retry = $create($listing, $buyer, $buyerAddress, $sellerAddress, new Money(0, Currency::EUR), 'same-request');

        $this->assertTrue($order->is($retry));
        $this->assertTrue($deadline->equalTo($retry->reservation_expires_at));
        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseCount('order_transitions', 1);

        $this->travel(15)->minutes();

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('stale reservation');
        $create($listing, $buyer, $buyerAddress, $sellerAddress, new Money(0, Currency::EUR), 'same-request');
    }

    public function test_self_purchase_and_idempotency_key_reuse_with_different_inputs_are_rejected(): void
    {
        [$listing, $buyer, $buyerAddress, $sellerAddress] = $this->orderInputs();
        $create = app(CreateOrder::class);

        $create($listing, $buyer, $buyerAddress, $sellerAddress, new Money(0, Currency::EUR), 'request-key');

        try {
            $create($listing, $buyer, $buyerAddress, $sellerAddress, new Money(100, Currency::EUR), 'request-key');
            $this->fail('Reusing a key for different inputs should fail.');
        } catch (DomainException $exception) {
            $this->assertStringContainsString('different order inputs', $exception->getMessage());
        }

        [$sellerListing, , , $ownAddress] = $this->orderInputs();
        $seller = $sellerListing->user;

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('own listing');
        $create($sellerListing, $seller, $ownAddress, $ownAddress, new Money(0, Currency::EUR), 'self-purchase');
    }

    public function test_buyer_cancellation_is_idempotent_and_releases_the_listing_for_a_new_order(): void
    {
        [$listing, $buyer, $buyerAddress, $sellerAddress] = $this->orderInputs();
        $order = app(CreateOrder::class)(
            $listing,
            $buyer,
            $buyerAddress,
            $sellerAddress,
            new Money(0, Currency::EUR),
            'buyer-cancellation',
        );

        $cancel = app(CancelOrderReservation::class);
        $cancelled = $cancel($order, $buyer, ReservationCancellationReason::BuyerCancelled);
        $cancel($cancelled, $buyer, ReservationCancellationReason::BuyerCancelled);

        $this->assertSame(OrderState::Cancelled, $cancelled->state);
        $this->assertFalse((bool) $cancelled->inventory_claim);
        $this->assertSame(ListingStatus::Active, $listing->fresh()->status);
        $this->assertDatabaseCount('order_transitions', 2);
        $this->assertDatabaseHas('order_transitions', [
            'order_id' => $order->id,
            'actor_id' => $buyer->id,
            'reason' => ReservationCancellationReason::BuyerCancelled->value,
            'listing_status' => ListingStatus::Active->value,
        ]);

        $nextBuyer = User::factory()->create();
        $nextAddress = $this->addressFor($nextBuyer, 'Next buyer');
        $nextOrder = app(CreateOrder::class)(
            $listing,
            $nextBuyer,
            $nextAddress,
            $sellerAddress,
            new Money(0, Currency::EUR),
            'new-reservation',
        );

        $this->assertNotSame($order->id, $nextOrder->id);
        $this->assertTrue($nextOrder->inventory_claim);
        $this->assertSame(ListingStatus::Reserved, $listing->fresh()->status);
    }

    public function test_admin_unavailable_cancellation_hides_the_listing_and_seller_cannot_cancel(): void
    {
        [$listing, $buyer, $buyerAddress, $sellerAddress] = $this->orderInputs();
        $order = app(CreateOrder::class)(
            $listing,
            $buyer,
            $buyerAddress,
            $sellerAddress,
            new Money(0, Currency::EUR),
            'admin-cancellation',
        );

        try {
            app(CancelOrderReservation::class)(
                $order,
                $listing->user,
                ReservationCancellationReason::ItemUnavailable,
                ListingStatus::Hidden,
            );
            $this->fail('The seller should not be able to cancel a reservation.');
        } catch (AuthorizationException) {
            $this->assertSame(OrderState::Created, $order->fresh()->state);
        }

        $admin = User::factory()->admin()->create();
        $cancelled = app(CancelOrderReservation::class)(
            $order,
            $admin,
            ReservationCancellationReason::ItemUnavailable,
            ListingStatus::Hidden,
        );

        $this->assertSame(OrderState::Cancelled, $cancelled->state);
        $this->assertSame(ListingStatus::Hidden, $listing->fresh()->status);
        $this->assertDatabaseHas('order_transitions', [
            'order_id' => $order->id,
            'actor_id' => $admin->id,
            'reason' => ReservationCancellationReason::ItemUnavailable->value,
            'listing_status' => ListingStatus::Hidden->value,
        ]);
    }

    public function test_expiry_cleanup_is_retry_safe_and_releases_only_due_reservations(): void
    {
        $this->travelTo('2026-08-12 10:00:00');
        [$listing, $buyer, $buyerAddress, $sellerAddress] = $this->orderInputs();
        $order = app(CreateOrder::class)(
            $listing,
            $buyer,
            $buyerAddress,
            $sellerAddress,
            new Money(0, Currency::EUR),
            'expiring-reservation',
        );

        try {
            app(ExpireOrderReservation::class)($order);
            $this->fail('A future reservation should not expire.');
        } catch (DomainException $exception) {
            $this->assertStringContainsString('has not passed', $exception->getMessage());
        }

        $this->travel(15)->minutes();

        try {
            app(CancelOrderReservation::class)($order, $buyer, ReservationCancellationReason::BuyerCancelled);
            $this->fail('A deadline-passed reservation should expire, not cancel.');
        } catch (DomainException $exception) {
            $this->assertStringContainsString('must be expired', $exception->getMessage());
        }

        $this->artisan('orders:expire-reservations')->assertSuccessful();
        $this->artisan('orders:expire-reservations')->assertSuccessful();

        $this->assertSame(OrderState::Expired, $order->fresh()->state);
        $this->assertFalse((bool) $order->fresh()->inventory_claim);
        $this->assertSame(ListingStatus::Active, $listing->fresh()->status);
        $this->assertDatabaseCount('order_transitions', 2);
    }

    public function test_release_failure_rolls_back_and_the_next_cleanup_retry_recovers(): void
    {
        $this->travelTo('2026-08-12 10:00:00');
        [$listing, $buyer, $buyerAddress, $sellerAddress] = $this->orderInputs();
        $order = app(CreateOrder::class)(
            $listing,
            $buyer,
            $buyerAddress,
            $sellerAddress,
            new Money(0, Currency::EUR),
            'cleanup-retry',
        );
        $this->travel(15)->minutes();
        config(['marketplace.fail_reservation_release_for_test' => true]);
        OrderTransition::creating(function (OrderTransition $transition): void {
            if (config('marketplace.fail_reservation_release_for_test') && $transition->reason === 'reservation_expired') {
                throw new LogicException('Simulated audit write failure.');
            }
        });

        try {
            app(ExpireOrderReservation::class)($order);
            $this->fail('The simulated audit failure should abort release.');
        } catch (LogicException $exception) {
            $this->assertSame('Simulated audit write failure.', $exception->getMessage());
        }

        $this->assertSame(OrderState::Created, $order->fresh()->state);
        $this->assertTrue($order->fresh()->inventory_claim);
        $this->assertSame(ListingStatus::Reserved, $listing->fresh()->status);

        config(['marketplace.fail_reservation_release_for_test' => false]);
        app(ExpireOrderReservation::class)($order);

        $this->assertSame(OrderState::Expired, $order->fresh()->state);
        $this->assertSame(ListingStatus::Active, $listing->fresh()->status);
    }

    public function test_only_participants_and_administrators_can_view_an_order(): void
    {
        [$listing, $buyer, $buyerAddress, $sellerAddress] = $this->orderInputs();
        $order = app(CreateOrder::class)(
            $listing,
            $buyer,
            $buyerAddress,
            $sellerAddress,
            new Money(0, Currency::EUR),
            'policy-order',
        );

        $this->assertTrue(Gate::forUser($buyer)->allows('view', $order));
        $this->assertTrue(Gate::forUser($listing->user)->allows('view', $order));
        $this->assertTrue(Gate::forUser(User::factory()->admin()->create())->allows('view', $order));
        $this->assertFalse(Gate::forUser(User::factory()->create())->allows('view', $order));
    }

    /** @return array{Listing, User, Address, Address} */
    private function orderInputs(): array
    {
        $seller = User::factory()->create(['name' => 'Seller Before']);
        $buyer = User::factory()->create(['name' => 'Buyer Before']);
        $listing = Listing::factory()->for($seller)->create([
            'title' => 'Snapshot coat',
            'price_amount' => 10_000,
            'currency' => Currency::EUR,
            'status' => ListingStatus::Active,
        ]);

        $buyerAddress = $buyer->addresses()->create([
            'label' => 'Home',
            'recipient_name' => 'Buyer Before',
            'phone' => '+355691234567',
            'street' => 'Buyer Street 1',
            'city' => 'Durres',
            'postal_code' => '2001',
        ]);
        $sellerAddress = $seller->addresses()->create([
            'label' => 'Home',
            'recipient_name' => 'Seller Before',
            'phone' => '+355692345678',
            'street' => 'Seller Street 2',
            'city' => 'Tirana',
            'postal_code' => '1001',
        ]);

        return [$listing, $buyer, $buyerAddress, $sellerAddress];
    }

    private function addressFor(User $user, string $name): Address
    {
        return $user->addresses()->create([
            'label' => 'Home',
            'recipient_name' => $name,
            'phone' => '+355693456789',
            'street' => $name.' Street',
            'city' => 'Tirana',
            'postal_code' => '1001',
        ]);
    }
}
