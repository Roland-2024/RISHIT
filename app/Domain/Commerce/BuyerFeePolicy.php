<?php

namespace App\Domain\Commerce;

use App\Domain\Shared\Currency;
use App\Domain\Shared\Money;

enum BuyerFeePolicy: string
{
    case NoFeeV1 = 'buyer_fee_none_v1';

    public function fee(): Money
    {
        return new Money(0, Currency::EUR);
    }

    /** @return array<string, int|string> */
    public function snapshot(): array
    {
        return [
            'version' => $this->value,
            'calculation' => 'fixed',
            'fee_amount' => 0,
            'tax_amount' => 0,
            'display_amount' => 0,
            'refundable_amount' => 0,
            'currency' => Currency::EUR->value,
        ];
    }
}
