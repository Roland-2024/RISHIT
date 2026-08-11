<?php

namespace Tests\Unit;

use App\Domain\Shared\Currency;
use App\Domain\Shared\Money;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class MoneyTest extends TestCase
{
    #[DataProvider('amounts')]
    public function test_money_formats_integer_minor_units(int $amount, Currency $currency, string $expected): void
    {
        $this->assertSame($expected, new Money($amount, $currency)->format());
    }

    public function test_decimal_money_is_parsed_without_floating_point(): void
    {
        $money = Money::fromDecimal('1250,50', Currency::EUR);

        $this->assertSame(125050, $money->amount);
        $this->assertSame(Currency::EUR, $money->currency);
        $this->assertSame('1250.50', $money->decimal());
    }

    public static function amounts(): array
    {
        return [
            'legacy whole lek' => [500000, Currency::ALL, '5,000 Lek'],
            'legacy lek qindarka' => [500050, Currency::ALL, '5,000.50 Lek'],
            'euro cents' => [5400, Currency::EUR, '€54.00'],
            'negative refund' => [-125, Currency::EUR, '-€1.25'],
        ];
    }
}
