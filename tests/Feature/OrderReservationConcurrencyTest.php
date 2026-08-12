<?php

namespace Tests\Feature;

use App\Application\Commerce\CreateOrder;
use App\Domain\Catalog\ListingStatus;
use App\Domain\Shared\Currency;
use App\Domain\Shared\Money;
use App\Models\Address;
use App\Models\Listing;
use App\Models\Order;
use App\Models\User;
use DomainException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Throwable;

class OrderReservationConcurrencyTest extends TestCase
{
    use DatabaseMigrations;

    public function test_two_buyers_racing_for_one_listing_produce_one_inventory_claim(): void
    {
        if (DB::getDriverName() !== 'mysql' || ! function_exists('pcntl_fork')) {
            $this->markTestSkipped('This concurrency proof requires MySQL and pcntl.');
        }

        $seller = User::factory()->create();
        $listing = Listing::factory()->for($seller)->create([
            'currency' => Currency::EUR,
            'status' => ListingStatus::Active,
        ]);
        $sellerAddress = $this->addressFor($seller, 'Seller');
        $buyers = User::factory()->count(2)->create();
        $buyerAddresses = $buyers->map(fn (User $buyer, int $index) => $this->addressFor($buyer, 'Buyer '.($index + 1)));
        $start = sys_get_temp_dir().DIRECTORY_SEPARATOR.'rishit-reservation-start-'.bin2hex(random_bytes(8));
        $results = [
            $start.'-one',
            $start.'-two',
        ];
        $pids = [];

        try {
            foreach ($buyers as $index => $buyer) {
                $pid = pcntl_fork();

                if ($pid === 0) {
                    while (! file_exists($start)) {
                        usleep(1_000);
                    }

                    DB::purge();

                    try {
                        $order = app(CreateOrder::class)(
                            Listing::query()->findOrFail($listing->id),
                            User::query()->findOrFail($buyer->id),
                            Address::query()->findOrFail($buyerAddresses[$index]->id),
                            Address::query()->findOrFail($sellerAddress->id),
                            new Money(0, Currency::EUR),
                            'concurrent-buyer-'.($index + 1),
                        );
                        file_put_contents($results[$index], 'won:'.$order->id);
                    } catch (Throwable $exception) {
                        file_put_contents($results[$index], 'lost:'.get_class($exception));
                    }

                    exit(0);
                }

                $this->assertGreaterThan(0, $pid);
                $pids[] = $pid;
            }

            touch($start);

            foreach ($pids as $pid) {
                pcntl_waitpid($pid, $status);
                $this->assertTrue(pcntl_wifexited($status));
                $this->assertSame(0, pcntl_wexitstatus($status));
            }

            DB::purge();
            $outcomes = array_map(fn (string $path) => file_get_contents($path), $results);

            $this->assertCount(1, array_filter($outcomes, fn (string $outcome) => str_starts_with($outcome, 'won:')));
            $this->assertSame(['lost:'.DomainException::class], array_values(array_filter($outcomes, fn (string $outcome) => str_starts_with($outcome, 'lost:'))));
            $this->assertSame(1, Order::query()->where('inventory_claim', true)->count());
            $this->assertSame(1, Order::query()->count());
            $this->assertSame(ListingStatus::Reserved, $listing->fresh()->status);
        } finally {
            foreach ([$start, ...$results] as $path) {
                if (file_exists($path)) {
                    unlink($path);
                }
            }
        }
    }

    private function addressFor(User $user, string $name): Address
    {
        return $user->addresses()->create([
            'label' => 'Home',
            'recipient_name' => $name,
            'phone' => '+355691234567',
            'street' => $name.' Street',
            'city' => 'Tirana',
            'postal_code' => '1001',
        ]);
    }
}
