<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\View;

class ProfileController extends Controller
{
    public function __invoke(string $locale, User $user): View
    {
        return view('profiles.show', [
            'seller' => $user,
            'listings' => $user->listings()->visible()->withCardData(auth()->id())->latest()->paginate(24),
        ]);
    }
}
