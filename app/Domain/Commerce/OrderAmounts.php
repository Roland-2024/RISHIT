<?php

namespace App\Domain\Commerce;

use App\Domain\Shared\Currency;
use App\Domain\Shared\Money;
use App\Models\Listing;
use InvalidArgumentException;
use OverflowException;

final readonly class OrderAmounts
{
    private function __construct(
        public int $item,
        public int $shipping,
        public int $buyerFee,
        public int $sellerFee,
        public int $total,
        public int $sellerPayable,
        public Currency $currency,
    ) {}

    public static function forListing(
        Listing $listing,
        Money $shipping,
        BuyerFeePolicy $buyerFeePolicy = BuyerFeePolicy::NoFeeV1,
    ): self {
        if ($listing->currency !== Currency::EUR || $shipping->currency !== Currency::EUR) {
            throw new InvalidArgumentException('Orders require EUR listing and shipping amounts.');
        }

        if ($listing->price_amount <= 0 || $shipping->amount < 0) {
            throw new InvalidArgumentException('Order amounts must be non-negative and the item price must be positive.');
        }

        $buyerFee = $buyerFeePolicy->fee()->amount;
        $sellerFee = 0;

        if ($listing->price_amount > PHP_INT_MAX - $shipping->amount - $buyerFee) {
            throw new OverflowException('Order total exceeds the supported integer range.');
        }

        return new self(
            item: $listing->price_amount,
            shipping: $shipping->amount,
            buyerFee: $buyerFee,
            sellerFee: $sellerFee,
            total: $listing->price_amount + $shipping->amount + $buyerFee,
            sellerPayable: $listing->price_amount - $sellerFee,
            currency: Currency::EUR,
        );
    }
}
