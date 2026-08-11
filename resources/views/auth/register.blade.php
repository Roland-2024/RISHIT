<x-layout :title="__('ui.auth.register_title').' — RISHIT'" :indexable="false">
    <section class="mx-auto max-w-lg px-4 py-16 sm:px-6 lg:py-24">
        <div class="rounded-[2rem] border border-ink/10 bg-white p-7 shadow-sm sm:p-10">
            <h1 class="text-4xl font-black tracking-[-0.04em] text-forest">{{ __('ui.auth.register_title') }}</h1>
            <p class="mt-2 text-ink/60">{{ __('ui.auth.register_body') }}</p>
            <form method="POST" action="{{ route('register') }}" class="mt-8 space-y-5">
                @csrf
                @foreach (['name' => 'text', 'username' => 'text', 'email' => 'email'] as $field => $type)
                    <div>
                        <label for="{{ $field }}" class="text-sm font-bold">{{ __("ui.auth.{$field}") }}</label>
                        <input id="{{ $field }}" name="{{ $field }}" type="{{ $type }}" value="{{ old($field) }}" required @if ($loop->first) autofocus @endif autocomplete="{{ $field === 'name' ? 'name' : $field }}" class="mt-2 w-full rounded-xl border border-ink/20 px-4 py-3 focus:border-forest focus:outline-none focus:ring-2 focus:ring-forest/20">
                        @error($field) <p class="mt-2 text-sm font-semibold text-red-700">{{ $message }}</p> @enderror
                    </div>
                @endforeach
                <div>
                    <label for="password" class="text-sm font-bold">{{ __('ui.auth.password') }}</label>
                    <input id="password" name="password" type="password" required autocomplete="new-password" class="mt-2 w-full rounded-xl border border-ink/20 px-4 py-3 focus:border-forest focus:outline-none focus:ring-2 focus:ring-forest/20">
                    @error('password') <p class="mt-2 text-sm font-semibold text-red-700">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="password_confirmation" class="text-sm font-bold">{{ __('ui.auth.password_confirmation') }}</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="mt-2 w-full rounded-xl border border-ink/20 px-4 py-3 focus:border-forest focus:outline-none focus:ring-2 focus:ring-forest/20">
                </div>
                <button class="w-full rounded-full bg-forest px-5 py-3 font-bold text-white hover:bg-ink">{{ __('ui.auth.register') }}</button>
            </form>
            <p class="mt-6 text-center text-sm text-ink/60">{{ __('ui.auth.already') }} <a href="{{ route('login') }}" class="font-bold text-forest underline">{{ __('ui.auth.login') }}</a></p>
        </div>
    </section>
</x-layout>
