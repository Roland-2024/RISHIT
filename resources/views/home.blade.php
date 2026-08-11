<x-layout>
    <section class="overflow-hidden border-b border-ink/10">
        <div class="mx-auto grid max-w-7xl items-center gap-10 px-4 py-14 sm:px-6 md:grid-cols-[1.1fr_0.9fr] md:py-20 lg:px-8 lg:py-28">
            <div>
                <p class="mb-4 text-xs font-black uppercase tracking-[0.24em] text-coral">{{ __('ui.hero.eyebrow') }}</p>
                <h1 class="max-w-4xl text-5xl font-black leading-[0.94] tracking-[-0.055em] text-forest sm:text-6xl lg:text-7xl">{{ __('ui.hero.title') }}</h1>
                <p class="mt-7 max-w-2xl text-lg leading-8 text-ink/70">{{ __('ui.hero.body') }}</p>
                <p class="mt-5 inline-flex rounded-full bg-lilac px-4 py-2 text-sm font-black text-forest">{{ __('ui.hero.fee_promise') }}</p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ auth()->check() ? route('listings.create') : route('register') }}" class="rounded-full bg-coral px-6 py-3 font-bold text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-coral-dark">{{ __('ui.hero.primary') }}</a>
                    <a href="{{ route('catalog.index') }}" class="rounded-full border border-forest px-6 py-3 font-bold text-forest transition hover:bg-forest hover:text-white">{{ __('catalog.browse_all') }}</a>
                </div>
            </div>

            <div class="relative mx-auto aspect-[4/5] w-full max-w-md" aria-hidden="true">
                <div class="absolute inset-x-10 top-4 rotate-3 rounded-[2rem] bg-lilac p-7 shadow-xl">
                    <div class="aspect-square rounded-[1.5rem] border border-ink/10 bg-sand p-6">
                        <div class="flex h-full flex-col justify-between rounded-[1.2rem] bg-forest p-6 text-sand">
                            <span class="text-sm font-bold uppercase tracking-[0.2em]">RISHIT</span>
                            <strong class="text-5xl font-black leading-none tracking-[-0.06em]">{{ __('ui.hero.visual_one') }}</strong>
                        </div>
                    </div>
                </div>
                <div class="absolute bottom-3 left-0 w-3/4 -rotate-6 rounded-[2rem] bg-coral p-6 text-white shadow-xl">
                    <strong class="block text-4xl font-black tracking-[-0.05em]">{{ __('ui.hero.visual_two') }}</strong>
                    <span class="mt-10 block text-sm font-semibold">{{ __('ui.hero.visual_note') }}</span>
                </div>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
        <div class="flex items-end justify-between gap-5">
            <div>
                <p class="text-xs font-black uppercase tracking-[0.24em] text-coral">{{ __('catalog.eyebrow') }}</p>
                <h2 class="mt-3 text-4xl font-black tracking-[-0.045em] text-forest sm:text-5xl">{{ __('catalog.newest') }}</h2>
                <p class="mt-3 text-ink/60">{{ __('catalog.newest_note') }}</p>
            </div>
            <a href="{{ route('catalog.index') }}" class="hidden shrink-0 rounded-full border border-forest px-5 py-2.5 font-bold text-forest sm:block">{{ __('catalog.browse_all') }}</a>
        </div>

        @if ($listings->isEmpty())
            <div class="mt-9 rounded-3xl border border-dashed border-ink/20 p-10 text-center">
                <p class="font-bold text-forest">{{ __('catalog.empty') }}</p>
            </div>
        @else
            <div class="mt-9 grid grid-cols-2 gap-x-4 gap-y-10 sm:grid-cols-3 lg:grid-cols-4">
                @foreach ($listings as $listing)<x-listing-card :listing="$listing" />@endforeach
            </div>
        @endif
    </section>

    <section class="border-y border-ink/10 bg-white/55">
        <div class="mx-auto grid max-w-7xl gap-12 px-4 py-14 sm:px-6 md:grid-cols-2 lg:px-8">
            <div>
                <h2 class="text-2xl font-black text-forest">{{ __('catalog.popular_categories') }}</h2>
                <div class="mt-6 flex flex-wrap gap-2">
                    @foreach ($categories as $category)
                        <a href="{{ route('catalog.index', ['category' => $category->slug]) }}" class="rounded-full border border-ink/15 bg-sand px-4 py-2 font-semibold hover:border-forest">{{ $category->label() }}</a>
                    @endforeach
                </div>
            </div>
            <div>
                <h2 class="text-2xl font-black text-forest">{{ __('catalog.popular_brands') }}</h2>
                <div class="mt-6 flex flex-wrap gap-2">
                    @foreach ($brands as $brand)
                        <a href="{{ route('catalog.index', ['brand' => $brand->slug]) }}" class="rounded-full border border-ink/15 bg-sand px-4 py-2 font-semibold hover:border-forest">{{ $brand->name }}</a>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section id="how-it-works" class="bg-forest text-sand">
        <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
            <p class="text-xs font-black uppercase tracking-[0.24em] text-lilac">{{ __('ui.steps.eyebrow') }}</p>
            <h2 class="mt-3 text-4xl font-black tracking-[-0.045em] sm:text-5xl">{{ __('ui.steps.title') }}</h2>
            <ol class="mt-10 grid gap-8 md:grid-cols-3">
                @foreach (__('ui.steps.list') as $step)
                    <li class="border-t border-sand/25 pt-5">
                        <span class="text-sm font-black text-coral">0{{ $loop->iteration }}</span>
                        <h3 class="mt-5 text-2xl font-black">{{ $step['title'] }}</h3>
                        <p class="mt-3 leading-7 text-sand/70">{{ $step['body'] }}</p>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
        <div class="rounded-[2rem] bg-lilac p-8 sm:p-12 lg:grid lg:grid-cols-[0.7fr_1.3fr] lg:gap-12">
            <p class="text-xs font-black uppercase tracking-[0.24em] text-forest/65">{{ __('ui.trust.eyebrow') }}</p>
            <div>
                <h2 class="mt-4 text-3xl font-black tracking-[-0.04em] text-forest lg:mt-0 lg:text-5xl">{{ __('ui.trust.title') }}</h2>
                <p class="mt-5 max-w-3xl leading-7 text-forest/70">{{ __('ui.trust.body') }}</p>
            </div>
        </div>
    </section>
</x-layout>
