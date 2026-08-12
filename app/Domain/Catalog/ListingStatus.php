<?php

namespace App\Domain\Catalog;

enum ListingStatus: string
{
    case Active = 'active';
    case Hidden = 'hidden';
    case Reserved = 'reserved';
    case Sold = 'sold';
}
