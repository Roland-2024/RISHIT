<x-layout :title="$listing->title.' — RISHIT'" :description="Illuminate\Support\Str::limit($listing->description, 155)">
    <div class="mx-auto grid max-w-7xl gap-10 px-4 py-10 sm:px-6 lg:grid-cols-[1.2fr_0.8fr] lg:px-8">
        <section>
            @if ($listing->images->isNotEmpty())
                <div class="grid gap-3 sm:grid-cols-2">
                    @foreach ($listing->images as $image)
                        <img src="{{ $image->url() }}" alt="{{ $image->alt_text ?: $listing->title }}" class="w-full rounded-2xl bg-white object-cover {{ $loop->first ? 'sm:col-span-2 max-h-[48rem]' : 'aspect-[3/4]' }}">
                    @endforeach
                </div>
            @else
                <div class="flex aspect-[4/3] items-end rounded-3xl bg-gradient-to-br from-lilac via-sand to-coral/55 p-10">
                    <span class="text-8xl font-black text-forest/70">{{ mb_substr($listing->title, 0, 1) }}</span>
                </div>
            @endif
        </section>

        <aside class="lg:sticky lg:top-6 lg:self-start">
            <p class="text-sm font-bold text-coral"><a href="{{ route('categories.show', $listing->category) }}">{{ $listing->category->label() }}</a></p>
            <h1 class="mt-2 text-4xl font-black tracking-[-0.04em] text-forest">{{ $listing->title }}</h1>
            <x-money :amount="$listing->price_amount" :currency="$listing->currency" class="mt-5 block text-3xl font-black text-forest" />

            @if ($reason = $listing->publicUnavailabilityReason())
                <p class="mt-5 rounded-2xl bg-coral/10 p-4 text-sm font-bold leading-6 text-coral-dark">{{ __('catalog.unavailable.'.$reason) }}</p>
            @endif

            <dl class="mt-8 grid grid-cols-2 gap-x-5 gap-y-4 rounded-2xl border border-ink/10 bg-white p-5 text-sm">
                <div><dt class="text-ink/50">{{ __('catalog.condition') }}</dt><dd class="mt-1 font-bold">{{ __('catalog.condition_labels.'.$listing->condition->value) }}</dd></div>
                <div><dt class="text-ink/50">{{ __('catalog.brand') }}</dt><dd class="mt-1 font-bold">@if ($listing->brand)<a href="{{ route('brands.show', $listing->brand) }}">{{ $listing->brand->name }}</a>@else{{ __('catalog.no_brand') }}@endif</dd></div>
                @if ($listing->size)<div><dt class="text-ink/50">{{ __('catalog.size') }}</dt><dd class="mt-1 font-bold">{{ $listing->size }}</dd></div>@endif
                @if ($listing->color)<div><dt class="text-ink/50">{{ __('catalog.color') }}</dt><dd class="mt-1 font-bold">{{ $listing->color }}</dd></div>@endif
            </dl>

            <section class="mt-8">
                <h2 class="font-black text-forest">{{ __('catalog.description') }}</h2>
                <p class="mt-3 whitespace-pre-line leading-7 text-ink/75">{{ $listing->description }}</p>
            </section>

            <a href="{{ route('profiles.show', $listing->user) }}" class="mt-8 flex items-center justify-between rounded-2xl border border-ink/10 bg-white p-5 hover:border-forest/40">
                <span><span class="block text-xs text-ink/50">{{ __('catalog.seller') }}</span><strong class="mt-1 block text-forest">{{ '@'.$listing->user->username }}</strong></span>
                <span aria-hidden="true">→</span>
            </a>

            @if ($listing->isPubliclyVisible())
            @auth
                @php($isFavorited = (bool) $listing->is_favorited)
                <form method="POST" action="{{ $isFavorited ? route('favorites.destroy', $listing) : route('favorites.store', $listing) }}" class="mt-4">
                    @csrf
                    @if ($isFavorited) @method('DELETE') @endif
                    <button class="w-full rounded-full border border-forest px-6 py-3 font-bold text-forest hover:bg-forest hover:text-white">{{ $isFavorited ? __('catalog.unfavorite') : __('catalog.favorite') }}</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="mt-4 block rounded-full border border-forest px-6 py-3 text-center font-bold text-forest">{{ __('catalog.favorite') }}</a>
            @endauth

            <p class="mt-4 rounded-2xl bg-lilac/60 p-4 text-sm font-semibold leading-6 text-forest">{{ __('catalog.buy_unavailable') }}</p>
            @endif

            @can('update', $listing)
                <div class="mt-5 flex gap-3">
                    <a href="{{ route('listings.edit', $listing) }}" class="rounded-full bg-forest px-5 py-2.5 font-bold text-white">{{ __('catalog.edit') }}</a>
                    <a href="{{ route('my-listings.index') }}" class="rounded-full px-5 py-2.5 font-bold text-forest">{{ __('catalog.my_listings') }}</a>
                </div>
            @endcan
        </aside>
    </div>
</x-layout>
