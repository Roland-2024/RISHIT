<x-layout :title="__('ui.auth.login_title').' — RISHIT'" :indexable="false">
    <section class="mx-auto max-w-lg px-4 py-16 sm:px-6 lg:py-24">
        <div class="rounded-[2rem] border border-ink/10 bg-white p-7 shadow-sm sm:p-10">
            <h1 class="text-4xl font-black tracking-[-0.04em] text-forest">{{ __('ui.auth.login_title') }}</h1>
            <p class="mt-2 text-ink/60">{{ __('ui.auth.login_body') }}</p>
            <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-5">
                @csrf
                <div>
                    <label for="email" class="text-sm font-bold">{{ __('ui.auth.email') }}</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="email" class="mt-2 w-full rounded-xl border border-ink/20 px-4 py-3 focus:border-forest focus:outline-none focus:ring-2 focus:ring-forest/20">
                    @error('email') <p class="mt-2 text-sm font-semibold text-red-700">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="password" class="text-sm font-bold">{{ __('ui.auth.password') }}</label>
                    <input id="password" name="password" type="password" required autocomplete="current-password" class="mt-2 w-full rounded-xl border border-ink/20 px-4 py-3 focus:border-forest focus:outline-none focus:ring-2 focus:ring-forest/20">
                    @error('password') <p class="mt-2 text-sm font-semibold text-red-700">{{ $message }}</p> @enderror
                </div>
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="remember" class="rounded border-ink/30 text-forest"> {{ __('ui.auth.remember') }}</label>
                <button class="w-full rounded-full bg-forest px-5 py-3 font-bold text-white hover:bg-ink">{{ __('ui.auth.login') }}</button>
            </form>
            <p class="mt-6 text-center text-sm text-ink/60">{{ __('ui.auth.new_here') }} <a href="{{ route('register') }}" class="font-bold text-forest underline">{{ __('ui.auth.register') }}</a></p>
        </div>
    </section>
</x-layout>
