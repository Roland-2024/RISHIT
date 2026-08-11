<?php

namespace App\Http\Controllers;

use App\Domain\Catalog\CatalogFilters;
use App\Domain\Catalog\ListingCondition;
use App\Domain\Shared\Currency;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Listing;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function __invoke(Request $request): View
    {
        $filters = $request->validate(CatalogFilters::rules());
        $filters['currency'] = Currency::EUR->value;

        return view('catalog.index', [
            'listings' => Listing::query()->visible()->withCardData(auth()->id())->filter($filters)->paginate(24)->withQueryString(),
            'categories' => Category::query()->with(['children' => fn ($query) => $query->where('is_active', true)])->whereNull('parent_id')->where('is_active', true)->orderBy('sort_order')->get(),
            'brands' => Brand::query()->orderBy('name')->get(),
            'conditions' => ListingCondition::cases(),
            'filters' => $filters,
        ]);
    }
}
