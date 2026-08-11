<x-layout :title="__('catalog.meta_title')" :description="__('catalog.meta_description')" :indexable="request()->query() === []">
    <section class="border-b border-ink/10 bg-white/50">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <p class="text-xs font-black uppercase tracking-[0.24em] text-coral">{{ __('catalog.eyebrow') }}</p>
            <h1 class="mt-3 max-w-3xl text-4xl font-black tracking-[-0.05em] text-forest sm:text-6xl">{{ __('catalog.title') }}</h1>
        </div>
    </section>

    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <form method="GET" action="{{ route('catalog.index') }}" class="grid gap-4 rounded-3xl border border-ink/10 bg-white p-5 shadow-sm md:grid-cols-2 lg:grid-cols-4">
            <label class="lg:col-span-2">
                <span class="mb-1 block text-xs font-bold uppercase tracking-wider text-ink/55">{{ __('catalog.search') }}</span>
                <input name="q" type="search" value="{{ $filters['q'] ?? '' }}" class="w-full rounded-xl border border-ink/15 px-4 py-3" placeholder="{{ __('catalog.search') }}">
            </label>
            <label>
                <span class="mb-1 block text-xs font-bold uppercase tracking-wider text-ink/55">{{ __('catalog.category') }}</span>
                <select name="category" class="w-full rounded-xl border border-ink/15 bg-white px-4 py-3">
                    <option value="">{{ __('catalog.all_categories') }}</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->slug }}" @selected(($filters['category'] ?? '') === $category->slug)>{{ $category->label() }}</option>
                        @foreach ($category->children as $child)
                            <option value="{{ $child->slug }}" @selected(($filters['category'] ?? '') === $child->slug)>— {{ $child->label() }}</option>
                        @endforeach
                    @endforeach
                </select>
            </label>
            <label>
                <span class="mb-1 block text-xs font-bold uppercase tracking-wider text-ink/55">{{ __('catalog.brand') }}</span>
                <select name="brand" class="w-full rounded-xl border border-ink/15 bg-white px-4 py-3">
                    <option value="">{{ __('catalog.all_brands') }}</option>
                    @foreach ($brands as $brand)
                        <option value="{{ $brand->slug }}" @selected(($filters['brand'] ?? '') === $brand->slug)>{{ $brand->name }}</option>
                    @endforeach
                </select>
            </label>
            <label>
                <span class="mb-1 block text-xs font-bold uppercase tracking-wider text-ink/55">{{ __('catalog.condition') }}</span>
                <select name="condition" class="w-full rounded-xl border border-ink/15 bg-white px-4 py-3">
                    <option value="">{{ __('catalog.all_conditions') }}</option>
                    @foreach ($conditions as $condition)
                        <option value="{{ $condition->value }}" @selected(($filters['condition'] ?? '') === $condition->value)>{{ __('catalog.condition_labels.'.$condition->value) }}</option>
                    @endforeach
                </select>
            </label>
            <label>
                <span class="mb-1 block text-xs font-bold uppercase tracking-wider text-ink/55">{{ __('catalog.min_price') }}</span>
                <input name="min_price" inputmode="decimal" value="{{ $filters['min_price'] ?? '' }}" class="w-full rounded-xl border border-ink/15 px-4 py-3">
            </label>
            <label>
                <span class="mb-1 block text-xs font-bold uppercase tracking-wider text-ink/55">{{ __('catalog.max_price') }}</span>
                <input name="max_price" inputmode="decimal" value="{{ $filters['max_price'] ?? '' }}" class="w-full rounded-xl border border-ink/15 px-4 py-3">
            </label>
            <label>
                <span class="mb-1 block text-xs font-bold uppercase tracking-wider text-ink/55">{{ __('catalog.sort') }}</span>
                <select name="sort" class="w-full rounded-xl border border-ink/15 bg-white px-4 py-3">
                    <option value="newest" @selected(($filters['sort'] ?? 'newest') === 'newest')>{{ __('catalog.sort_newest') }}</option>
                    <option value="price_asc" @selected(($filters['sort'] ?? '') === 'price_asc')>{{ __('catalog.sort_price_asc') }}</option>
                    <option value="price_desc" @selected(($filters['sort'] ?? '') === 'price_desc')>{{ __('catalog.sort_price_desc') }}</option>
                </select>
            </label>
            <div class="flex items-end gap-3 lg:col-span-4">
                <button class="rounded-full bg-forest px-6 py-3 font-bold text-white">{{ __('catalog.apply') }}</button>
                <a href="{{ route('catalog.index') }}" class="rounded-full px-5 py-3 font-bold text-forest hover:bg-sand">{{ __('catalog.clear_filters') }}</a>
            </div>
        </form>

        <div class="mt-10 flex items-end justify-between gap-4">
            <h2 class="text-2xl font-black text-forest">{{ __('catalog.results', ['count' => $listings->total()]) }}</h2>
        </div>

        @if ($listings->isEmpty())
            <div class="mt-8 rounded-3xl border border-dashed border-ink/20 p-10 text-center">
                <p class="text-lg font-bold text-forest">{{ __('catalog.empty') }}</p>
            </div>
        @else
            <div class="mt-8 grid grid-cols-2 gap-x-4 gap-y-10 sm:grid-cols-3 lg:grid-cols-4">
                @foreach ($listings as $listing)
                    <x-listing-card :listing="$listing" />
                @endforeach
            </div>
            <div class="mt-12">{{ $listings->links() }}</div>
        @endif
    </div>
</x-layout>
