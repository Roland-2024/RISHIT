<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class MyListingsController extends Controller
{
    public function __invoke(Request $request): View
    {
        return view('listings.mine', [
            'listings' => $request->user()->listings()->with(['brand', 'category', 'images'])->latest()->paginate(24),
        ]);
    }
}
