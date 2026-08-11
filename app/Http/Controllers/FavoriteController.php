<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index(Request $request): View
    {
        return view('favorites.index', [
            'listings' => $request->user()->favorites()->visible()->withCardData($request->user()->id)->latest('favorites.created_at')->paginate(24),
        ]);
    }

    public function store(Request $request, string $locale, Listing $listing): RedirectResponse
    {
        abort_unless($listing->isPubliclyVisible(), 404);
        $request->user()->favorites()->syncWithoutDetaching($listing->getKey());

        return back();
    }

    public function destroy(Request $request, string $locale, Listing $listing): RedirectResponse
    {
        $request->user()->favorites()->detach($listing);

        return back();
    }
}
