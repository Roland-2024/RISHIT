<?php

namespace App\Http\Controllers\Api;

use App\Domain\Catalog\CatalogFilters;
use App\Domain\Shared\Currency;
use App\Http\Controllers\Controller;
use App\Http\Resources\ListingResource;
use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ListingController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = $request->validate(CatalogFilters::rules());
        $filters['currency'] = Currency::EUR->value;

        return ListingResource::collection(
            Listing::query()->visible()->withCardData()->filter($filters)->paginate(20)->withQueryString()
        );
    }

    public function show(Listing $listing): ListingResource
    {
        abort_unless($listing->status->value === 'active' && $listing->currency === Currency::EUR, 404);

        return new ListingResource($listing->load(['brand', 'category', 'images', 'user']));
    }
}
