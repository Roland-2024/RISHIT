<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveAddressRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\Address;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class AccountSettingsController extends Controller
{
    public function edit(): View
    {
        return view('account.edit', [
            'addresses' => request()->user()->addresses()->latest()->get(),
        ]);
    }

    public function updateProfile(UpdateProfileRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return redirect()->route('account.edit')->with('status', __('account.flash.profile'));
    }

    public function storeAddress(SaveAddressRequest $request): RedirectResponse
    {
        $request->user()->addresses()->create($request->validated());

        return redirect()->route('account.edit')->with('status', __('account.flash.address_created'));
    }

    public function updateAddress(SaveAddressRequest $request, string $locale, Address $address): RedirectResponse
    {
        Gate::authorize('update', $address);
        $address->update($request->validated());

        return redirect()->route('account.edit')->with('status', __('account.flash.address_updated'));
    }

    public function destroyAddress(string $locale, Address $address): RedirectResponse
    {
        Gate::authorize('delete', $address);
        $address->delete();

        return redirect()->route('account.edit')->with('status', __('account.flash.address_deleted'));
    }
}
