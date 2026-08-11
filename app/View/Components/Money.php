<?php

namespace App\View\Components;

use App\Domain\Shared\Currency;
use App\Domain\Shared\Money as MoneyValue;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

final class Money extends Component
{
    public readonly string $formatted;

    public function __construct(int $amount, Currency|string $currency)
    {
        $currency = is_string($currency) ? Currency::from($currency) : $currency;
        $this->formatted = new MoneyValue($amount, $currency)->format();
    }

    public function render(): View
    {
        return view('components.money');
    }
}
