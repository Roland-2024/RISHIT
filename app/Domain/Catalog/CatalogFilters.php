<?php

namespace App\Domain\Catalog;

use App\Domain\Shared\Currency;
use Illuminate\Validation\Rule;

final class CatalogFilters
{
    public static function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'string', Rule::exists('categories', 'slug')->where('is_active', true)],
            'brand' => ['nullable', 'string', Rule::exists('brands', 'slug')],
            'condition' => ['nullable', Rule::enum(ListingCondition::class)],
            'currency' => ['nullable', Rule::in([Currency::EUR->value])],
            'min_price' => ['nullable', 'regex:/^\d{1,7}(?:[.,]\d{1,2})?$/'],
            'max_price' => ['nullable', 'regex:/^\d{1,7}(?:[.,]\d{1,2})?$/'],
            'sort' => ['nullable', Rule::in(['newest', 'price_asc', 'price_desc'])],
        ];
    }
}
