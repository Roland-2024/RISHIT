<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Listing;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('home', [
            'listings' => Listing::query()->visible()->withCardData(auth()->id())->latest()->limit(12)->get(),
            'categories' => Category::query()->whereNull('parent_id')->where('is_active', true)->orderBy('sort_order')->get(),
            'brands' => Brand::query()->withCount(['listings' => fn ($query) => $query->visible()])->orderByDesc('listings_count')->limit(10)->get(),
        ]);
    }
}
