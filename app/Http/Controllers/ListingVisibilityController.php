<?php

namespace App\Http\Controllers;

use App\Domain\Catalog\ListingStatus;
use App\Models\Listing;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class ListingVisibilityController extends Controller
{
    public function __invoke(Request $request, string $locale, Listing $listing): RedirectResponse
    {
        Gate::authorize('update', $listing);
        abort_if($listing->status === ListingStatus::Sold, 409);
        $validated = $request->validate([
            'status' => ['required', Rule::in([ListingStatus::Active->value, ListingStatus::Hidden->value])],
        ]);
        $listing->update(['status' => $validated['status']]);

        return back()->with('status', __('catalog.flash.visibility'));
    }
}
