@props([
    'title' => __('ui.meta.title'),
    'description' => __('ui.meta.description'),
    'indexable' => true,
])

@php
    $currentRouteName = request()->route()?->getName() ?? 'home';
    $currentRouteParameters = request()->route()?->parameters() ?? [];
    $localeUrl = fn (string $locale) => route($currentRouteName, array_merge($currentRouteParameters, ['locale' => $locale]));
@endphp

<!doctype html>
<html lang="{{ app()->getLocale() }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    <meta name="description" content="{{ $description }}">
    @if ($indexable)
        <link rel="canonical" href="{{ url()->current() }}">
        <link rel="alternate" hreflang="sq" href="{{ $localeUrl('sq') }}">
        <link rel="alternate" hreflang="en" href="{{ $localeUrl('en') }}">
        <link rel="alternate" hreflang="x-default" href="{{ $localeUrl('sq') }}">
    @else
        <meta name="robots" content="noindex, nofollow">
    @endif
    <meta property="og:site_name" content="RISHIT">
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-sand text-ink antialiased">
    <header x-data="{ open: false }" class="border-b border-ink/10 bg-sand/95 backdrop-blur">
        <div class="mx-auto flex max-w-7xl items-center gap-4 px-4 py-4 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" class="shrink-0 text-2xl font-black tracking-[-0.08em] text-forest" aria-label="RISHIT">
                RISHIT<span class="text-coral">.</span>
            </a>

            <form method="GET" action="{{ route('catalog.index') }}" class="hidden min-w-0 flex-1 gap-2 md:flex">
                <label for="market-search" class="sr-only">{{ __('ui.nav.search') }}</label>
                <input id="market-search" name="q" type="search" value="{{ request('q') }}" placeholder="{{ __('ui.nav.search') }}" class="min-w-0 flex-1 rounded-full border border-ink/15 bg-white/70 px-5 py-2.5 text-sm placeholder:text-ink/45">
                <button class="rounded-full bg-forest px-4 py-2.5 text-sm font-bold text-white">{{ __('ui.nav.go') }}</button>
            </form>

            <nav class="ml-auto hidden items-center gap-2 md:flex" aria-label="Primary navigation">
                <a href="{{ $localeUrl(app()->getLocale() === 'sq' ? 'en' : 'sq') }}" class="rounded-full px-3 py-2 text-sm font-semibold hover:bg-ink/5">
                    {{ app()->getLocale() === 'sq' ? 'EN' : 'SQ' }}
                </a>
                @auth
                    <a href="{{ route('favorites.index') }}" class="rounded-full px-3 py-2 text-sm font-semibold hover:bg-ink/5">{{ __('ui.nav.favorites') }}</a>
                    <a href="{{ route('profiles.show', auth()->user()) }}" class="rounded-full px-3 py-2 text-sm font-semibold hover:bg-ink/5">{{ auth()->user()->username }}</a>
                    <a href="{{ route('listings.create') }}" class="rounded-full border border-forest px-4 py-2 text-sm font-bold text-forest hover:bg-forest hover:text-white">{{ __('ui.nav.sell') }}</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="rounded-full px-3 py-2 text-sm font-semibold hover:bg-ink/5">{{ __('ui.nav.logout') }}</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="rounded-full px-4 py-2 text-sm font-semibold hover:bg-ink/5">{{ __('ui.nav.sign_in') }}</a>
                    <a href="{{ route('register') }}" class="rounded-full border border-forest px-4 py-2 text-sm font-bold text-forest hover:bg-forest hover:text-white">{{ __('ui.nav.sell') }}</a>
                @endauth
            </nav>

            <button type="button" @click="open = !open" class="ml-auto rounded-full border border-ink/20 p-2 md:hidden" :aria-expanded="open" aria-controls="mobile-navigation">
                <span class="sr-only" x-text="open ? @js(__('ui.nav.close')) : @js(__('ui.nav.menu'))"></span>
                <svg aria-hidden="true" viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
            </button>
        </div>

        <nav id="mobile-navigation" x-cloak x-show="open" @click.outside="open = false" class="border-t border-ink/10 px-4 py-4 md:hidden" aria-label="Mobile navigation">
            <div class="flex flex-col gap-2">
                <form method="GET" action="{{ route('catalog.index') }}" class="flex gap-2">
                    <label for="mobile-market-search" class="sr-only">{{ __('ui.nav.search') }}</label>
                    <input id="mobile-market-search" name="q" type="search" value="{{ request('q') }}" placeholder="{{ __('ui.nav.search') }}" class="min-w-0 flex-1 rounded-full border border-ink/15 bg-white px-4 py-2">
                    <button class="rounded-full bg-forest px-4 py-2 font-bold text-white">{{ __('ui.nav.go') }}</button>
                </form>
                <a href="{{ $localeUrl(app()->getLocale() === 'sq' ? 'en' : 'sq') }}" class="rounded-xl px-3 py-2 font-semibold">{{ app()->getLocale() === 'sq' ? 'English' : 'Shqip' }}</a>
                @auth
                    <a href="{{ route('profiles.show', auth()->user()) }}" class="rounded-xl px-3 py-2 font-semibold">{{ __('ui.nav.profile') }}</a>
                    <a href="{{ route('favorites.index') }}" class="rounded-xl px-3 py-2 font-semibold">{{ __('ui.nav.favorites') }}</a>
                    <a href="{{ route('my-listings.index') }}" class="rounded-xl px-3 py-2 font-semibold">{{ __('ui.nav.my_listings') }}</a>
                    <a href="{{ route('listings.create') }}" class="rounded-xl bg-forest px-3 py-2 font-bold text-white">{{ __('ui.nav.sell') }}</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="w-full rounded-xl px-3 py-2 text-left font-semibold">{{ __('ui.nav.logout') }}</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="rounded-xl px-3 py-2 font-semibold">{{ __('ui.nav.sign_in') }}</a>
                    <a href="{{ route('register') }}" class="rounded-xl bg-forest px-3 py-2 font-bold text-white">{{ __('ui.nav.sell') }}</a>
                @endauth
            </div>
        </nav>
    </header>

    @if (session('status'))
        <div class="border-b border-forest/15 bg-white px-4 py-3 text-center text-sm font-semibold text-forest">{{ session('status') }}</div>
    @endif

    <main>{{ $slot }}</main>

    <footer class="border-t border-ink/10 bg-white/50">
        <div class="mx-auto flex max-w-7xl flex-col gap-3 px-4 py-8 text-sm text-ink/65 sm:px-6 md:flex-row md:items-center md:justify-between lg:px-8">
            <p><strong class="text-forest">RISHIT</strong> — {{ __('ui.footer.tagline') }}</p>
            <p>{{ __('ui.footer.phase') }} · {{ now()->year }}</p>
        </div>
    </footer>
</body>
</html>
