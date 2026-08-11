<x-layout :title="__('account.meta_title')" :description="__('account.intro')" :indexable="false">
    <div class="mx-auto max-w-4xl px-4 py-12 sm:px-6 lg:px-8">
        <p class="text-sm font-black uppercase tracking-[0.16em] text-coral">{{ __('account.eyebrow') }}</p>
        <h1 class="mt-2 text-4xl font-black tracking-[-0.04em] text-forest">{{ __('account.title') }}</h1>
        <p class="mt-3 max-w-2xl text-ink/65">{{ __('account.intro') }}</p>

        @if ($errors->any())
            <div class="mt-8 rounded-2xl border border-coral/35 bg-coral/10 p-4 text-sm text-coral-dark">
                <ul class="list-disc space-y-1 pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <section class="mt-10 rounded-3xl border border-ink/10 bg-white/70 p-6 sm:p-8">
            <h2 class="text-2xl font-black text-forest">{{ __('account.profile.title') }}</h2>
            <p class="mt-2 text-sm text-ink/60">{{ __('account.profile.one_account') }}</p>

            <form method="POST" action="{{ route('account.profile.update') }}" class="mt-6 grid gap-5 sm:grid-cols-2">
                @csrf
                @method('PUT')
                <label>
                    <span class="mb-2 block font-bold">{{ __('ui.auth.name') }}</span>
                    <input name="name" value="{{ old('name', auth()->user()->name) }}" required maxlength="100" autocomplete="name" class="w-full rounded-xl border border-ink/15 bg-white px-4 py-3">
                </label>
                <label>
                    <span class="mb-2 block font-bold">{{ __('ui.auth.username') }}</span>
                    <input name="username" value="{{ old('username', auth()->user()->username) }}" required minlength="3" maxlength="30" autocomplete="username" class="w-full rounded-xl border border-ink/15 bg-white px-4 py-3">
                </label>
                <label>
                    <span class="mb-2 block font-bold">{{ __('ui.auth.email') }}</span>
                    <input name="email" type="email" value="{{ old('email', auth()->user()->email) }}" required maxlength="255" autocomplete="email" class="w-full rounded-xl border border-ink/15 bg-white px-4 py-3">
                </label>
                <label>
                    <span class="mb-2 block font-bold">{{ __('account.profile.language') }}</span>
                    <select name="preferred_locale" required class="w-full rounded-xl border border-ink/15 bg-white px-4 py-3">
                        <option value="sq" @selected(old('preferred_locale', auth()->user()->preferred_locale) === 'sq')>Shqip</option>
                        <option value="en" @selected(old('preferred_locale', auth()->user()->preferred_locale) === 'en')>English</option>
                    </select>
                </label>
                <div class="sm:col-span-2">
                    <button class="rounded-full bg-forest px-6 py-3 font-black text-white hover:bg-forest/90">{{ __('account.profile.save') }}</button>
                </div>
            </form>
        </section>

        <section class="mt-8 rounded-3xl border border-ink/10 bg-white/70 p-6 sm:p-8">
            <h2 class="text-2xl font-black text-forest">{{ __('account.addresses.title') }}</h2>
            <p class="mt-2 text-sm text-ink/60">{{ __('account.addresses.privacy') }}</p>

            @if ($addresses->isEmpty())
                <p class="mt-6 rounded-2xl border border-dashed border-ink/20 p-6 text-center text-ink/60">{{ __('account.addresses.empty') }}</p>
            @else
                <div class="mt-6 space-y-4">
                    @foreach ($addresses as $address)
                        <details class="rounded-2xl border border-ink/10 bg-sand/60 p-5">
                            <summary class="cursor-pointer font-black text-forest">{{ $address->label }} · {{ $address->city }}</summary>
                            <div class="mt-5">
                                @include('account._address-form', ['address' => $address])
                                <form method="POST" action="{{ route('account.addresses.destroy', $address) }}" class="mt-4" onsubmit="return confirm(@js(__('account.address.delete_confirm')))">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-sm font-bold text-coral-dark underline">{{ __('account.address.delete') }}</button>
                                </form>
                            </div>
                        </details>
                    @endforeach
                </div>
            @endif

            <div class="mt-8 border-t border-ink/10 pt-8">
                <h3 class="mb-5 text-xl font-black text-forest">{{ __('account.addresses.add_title') }}</h3>
                @include('account._address-form')
            </div>
        </section>
    </div>
</x-layout>
