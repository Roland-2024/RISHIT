<?php

namespace App\Domain\Catalog;

enum ListingCondition: string
{
    case NewWithTags = 'new_with_tags';
    case NewWithoutTags = 'new_without_tags';
    case VeryGood = 'very_good';
    case Good = 'good';
    case Satisfactory = 'satisfactory';

    /** @return list<string> */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
