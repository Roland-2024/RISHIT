<?php

namespace App\Domain\Shared;

use InvalidArgumentException;

final readonly class Money
{
    public function __construct(
        public int $amount,
        public Currency $currency,
    ) {}

    public function format(): string
    {
        $absolute = abs($this->amount);
        $major = number_format(intdiv($absolute, 100), 0, '.', ',');
        $minor = $absolute % 100;
        $decimals = $this->currency === Currency::EUR || $minor > 0
            ? sprintf('.%02d', $minor)
            : '';
        $sign = $this->amount < 0 ? '-' : '';

        return match ($this->currency) {
            Currency::ALL => "{$sign}{$major}{$decimals} Lek",
            Currency::EUR => "{$sign}€{$major}{$decimals}",
        };
    }

    public function decimal(): string
    {
        $absolute = abs($this->amount);

        return ($this->amount < 0 ? '-' : '')
            .intdiv($absolute, 100)
            .'.'
            .sprintf('%02d', $absolute % 100);
    }

    public static function fromDecimal(string $value, Currency $currency): self
    {
        $normalized = str_replace(',', '.', trim($value));

        if (! preg_match('/^-?\d{1,12}(?:\.\d{1,2})?$/D', $normalized)) {
            throw new InvalidArgumentException('Invalid decimal money value.');
        }

        $negative = str_starts_with($normalized, '-');
        [$major, $minor] = array_pad(explode('.', ltrim($normalized, '-'), 2), 2, '');
        $amount = ((int) $major * 100) + (int) str_pad($minor, 2, '0');

        return new self($negative ? -$amount : $amount, $currency);
    }
}
