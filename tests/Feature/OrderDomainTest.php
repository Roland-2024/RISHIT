<?php

namespace Tests\Feature;

use App\Application\Commerce\CreateOrder;
use App\Application\Commerce\TransitionOrder;
use App\Domain\Catalog\ListingStatus;
use App\Domain\Commerce\BuyerFeePolicy;
use App\Domain\Commerce\OrderState;
use App\Domain\Shared\Currency;
use App\Domain\Shared\Money;
use App\Models\Address;
use App\Models\Listing;
use App\Models\User;
use DomainException;
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
        $this->assertSame(ListingStatus::Sold, $listing->fresh()->status);
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
        );

        try {
            app(TransitionOrder::class)($order, OrderState::Paid, $buyer);
            $this->fail('Skipping an order state should fail.');
        } catch (DomainException $exception) {
            $this->assertStringContainsString('created to paid', $exception->getMessage());
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

        $create($listing, $buyer, $buyerAddress, $sellerAddress, new Money(0, Currency::EUR));

        $this->expectException(DomainException::class);
        $create($listing, User::factory()->create(), $buyerAddress, $sellerAddress, new Money(0, Currency::EUR));
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
}
