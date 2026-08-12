<?php

namespace App\Domain\Commerce;

use App\Domain\Shared\Currency;
use App\Domain\Shared\Money;
use InvalidArgumentException;
use OverflowException;

final readonly class OrderAmounts
{
    private function __construct(
        public int $item,
        public int $shipping,
        public int $buyerFee,
        public int $sellerListingFee,
        public int $sellerSellingFee,
        public int $total,
        public int $sellerPayable,
        public Currency $currency,
    ) {}

    public static function calculate(
        Money $item,
        Money $shipping,
        BuyerFeePolicy $buyerFeePolicy = BuyerFeePolicy::NoFeeV1,
    ): self {
        $buyerFee = $buyerFeePolicy->fee();

        if ($item->currency !== $shipping->currency || $item->currency !== $buyerFee->currency || $item->currency !== Currency::EUR) {
            throw new InvalidArgumentException('Order amounts must use one EUR currency.');
        }

        if ($item->amount <= 0 || $shipping->amount < 0 || $buyerFee->amount < 0) {
            throw new InvalidArgumentException('Order amounts must be non-negative and the item price must be positive.');
        }

        $sellerListingFee = 0;
        $sellerSellingFee = 0;

        if ($item->amount > PHP_INT_MAX - $shipping->amount || $item->amount + $shipping->amount > PHP_INT_MAX - $buyerFee->amount) {
            throw new OverflowException('Order total exceeds the supported integer range.');
        }

        return new self(
            item: $item->amount,
            shipping: $shipping->amount,
            buyerFee: $buyerFee->amount,
            sellerListingFee: $sellerListingFee,
            sellerSellingFee: $sellerSellingFee,
            total: $item->amount + $shipping->amount + $buyerFee->amount,
            sellerPayable: $item->amount - $sellerListingFee - $sellerSellingFee,
            currency: Currency::EUR,
        );
    }

    public function sellerFees(): int
    {
        return $this->sellerListingFee + $this->sellerSellingFee;
    }

    /** @return array<string, array<string, int|string>|int|string> */
    public function snapshot(BuyerFeePolicy $buyerFeePolicy): array
    {
        return [
            'buyer' => $buyerFeePolicy->snapshot(),
            'item_amount' => $this->item,
            'shipping_amount' => $this->shipping,
            'buyer_fee_amount' => $this->buyerFee,
            'seller_listing_fee_amount' => $this->sellerListingFee,
            'seller_selling_fee_amount' => $this->sellerSellingFee,
            'seller_fee_amount' => $this->sellerFees(),
            'total_amount' => $this->total,
            'seller_payable_amount' => $this->sellerPayable,
            'currency' => $this->currency->value,
        ];
    }
}
