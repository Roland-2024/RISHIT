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
        return $this->render($request);
    }

    public function category(Request $request, string $locale, Category $category): View
    {
        abort_unless($category->is_active, 404);

        return $this->render($request, ['category' => $category->slug], [
            'metaTitle' => __('catalog.category_meta_title', ['category' => $category->label()]),
            'metaDescription' => __('catalog.category_meta_description', ['category' => $category->label()]),
            'heading' => $category->label(),
        ]);
    }

    public function brand(Request $request, string $locale, Brand $brand): View
    {
        return $this->render($request, ['brand' => $brand->slug], [
            'metaTitle' => __('catalog.brand_meta_title', ['brand' => $brand->name]),
            'metaDescription' => __('catalog.brand_meta_description', ['brand' => $brand->name]),
            'heading' => $brand->name,
        ]);
    }

    private function render(Request $request, array $forcedFilters = [], array $page = []): View
    {
        $filters = $request->validate(CatalogFilters::rules());
        $filters = array_replace($filters, $forcedFilters);
        $filters['currency'] = Currency::EUR->value;

        return view('catalog.index', [
            'listings' => Listing::query()->visible()->withCardData(auth()->id())->filter($filters)->paginate(24)->withQueryString(),
            'categories' => Category::query()->with(['children' => fn ($query) => $query->where('is_active', true)])->whereNull('parent_id')->where('is_active', true)->orderBy('sort_order')->get(),
            'brands' => Brand::query()->orderBy('name')->get(),
            'conditions' => ListingCondition::cases(),
            'filters' => $filters,
            'metaTitle' => $page['metaTitle'] ?? __('catalog.meta_title'),
            'metaDescription' => $page['metaDescription'] ?? __('catalog.meta_description'),
            'heading' => $page['heading'] ?? __('catalog.title'),
        ]);
    }
}
