<?php

namespace Tests\Unit;

use App\Domain\Commerce\BuyerFeePolicy;
use App\Domain\Commerce\OrderAmounts;
use App\Domain\Shared\Currency;
use App\Domain\Shared\Money;
use InvalidArgumentException;
use OverflowException;
use PHPUnit\Framework\TestCase;

class OrderAmountsTest extends TestCase
{
    public function test_it_calculates_one_auditable_eur_snapshot_with_exact_zero_fees(): void
    {
        $amounts = OrderAmounts::calculate(
            new Money(10_000, Currency::EUR),
            new Money(750, Currency::EUR),
        );

        $this->assertSame(0, $amounts->buyerFee);
        $this->assertSame(0, $amounts->sellerListingFee);
        $this->assertSame(0, $amounts->sellerSellingFee);
        $this->assertSame(10_750, $amounts->total);
        $this->assertSame(10_000, $amounts->sellerPayable);
        $this->assertSame([
            'buyer' => [
                'version' => 'buyer_fee_none_v1',
                'calculation' => 'fixed',
                'fee_amount' => 0,
                'tax_amount' => 0,
                'display_amount' => 0,
                'refundable_amount' => 0,
                'currency' => 'EUR',
            ],
            'item_amount' => 10_000,
            'shipping_amount' => 750,
            'buyer_fee_amount' => 0,
            'seller_listing_fee_amount' => 0,
            'seller_selling_fee_amount' => 0,
            'seller_fee_amount' => 0,
            'total_amount' => 10_750,
            'seller_payable_amount' => 10_000,
            'currency' => 'EUR',
        ], $amounts->snapshot(BuyerFeePolicy::NoFeeV1));
    }

    public function test_it_rejects_mixed_currencies_and_negative_inputs(): void
    {
        foreach ([
            [new Money(100, Currency::EUR), new Money(0, Currency::ALL)],
            [new Money(-1, Currency::EUR), new Money(0, Currency::EUR)],
            [new Money(100, Currency::EUR), new Money(-1, Currency::EUR)],
        ] as [$item, $shipping]) {
            try {
                OrderAmounts::calculate($item, $shipping);
                $this->fail('Invalid order money should be rejected.');
            } catch (InvalidArgumentException) {
                $this->addToAssertionCount(1);
            }
        }
    }

    public function test_it_rejects_integer_overflow(): void
    {
        $this->expectException(OverflowException::class);

        OrderAmounts::calculate(
            new Money(PHP_INT_MAX, Currency::EUR),
            new Money(1, Currency::EUR),
        );
    }
}
