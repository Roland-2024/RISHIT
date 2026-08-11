@php($editing = isset($address))

<form method="POST" action="{{ $editing ? route('account.addresses.update', $address) : route('account.addresses.store') }}" class="grid gap-5 sm:grid-cols-2">
    @csrf
    @if ($editing) @method('PUT') @endif

    <label>
        <span class="mb-2 block font-bold">{{ __('account.address.label') }}</span>
        <input name="label" value="{{ $editing ? $address->label : old('label') }}" required maxlength="50" class="w-full rounded-xl border border-ink/15 bg-white px-4 py-3">
    </label>
    <label>
        <span class="mb-2 block font-bold">{{ __('account.address.recipient_name') }}</span>
        <input name="recipient_name" value="{{ $editing ? $address->recipient_name : old('recipient_name', auth()->user()->name) }}" required maxlength="100" autocomplete="name" class="w-full rounded-xl border border-ink/15 bg-white px-4 py-3">
    </label>
    <label>
        <span class="mb-2 block font-bold">{{ __('account.address.phone') }}</span>
        <input name="phone" value="{{ $editing ? $address->phone : old('phone') }}" required maxlength="13" inputmode="tel" autocomplete="tel" placeholder="+355691234567" class="w-full rounded-xl border border-ink/15 bg-white px-4 py-3">
        <span class="mt-1 block text-sm text-ink/55">{{ __('account.address.phone_hint') }}</span>
    </label>
    <label>
        <span class="mb-2 block font-bold">{{ __('account.address.street') }}</span>
        <input name="street" value="{{ $editing ? $address->street : old('street') }}" required maxlength="255" autocomplete="street-address" class="w-full rounded-xl border border-ink/15 bg-white px-4 py-3">
    </label>
    <label>
        <span class="mb-2 block font-bold">{{ __('account.address.city') }}</span>
        <input name="city" value="{{ $editing ? $address->city : old('city') }}" required maxlength="100" autocomplete="address-level2" class="w-full rounded-xl border border-ink/15 bg-white px-4 py-3">
    </label>
    <label>
        <span class="mb-2 block font-bold">{{ __('account.address.postal_code') }}</span>
        <input name="postal_code" value="{{ $editing ? $address->postal_code : old('postal_code') }}" required maxlength="4" inputmode="numeric" pattern="[0-9]{4}" autocomplete="postal-code" class="w-full rounded-xl border border-ink/15 bg-white px-4 py-3">
    </label>
    <div class="sm:col-span-2">
        <p class="mb-4 text-sm text-ink/60">{{ __('account.address.country') }}</p>
        <button class="rounded-full bg-coral px-6 py-3 font-black text-white hover:bg-coral-dark">
            {{ $editing ? __('account.address.save') : __('account.address.add') }}
        </button>
    </div>
</form>
