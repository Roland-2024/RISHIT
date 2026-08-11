@props(['listing'])

@php
    $image = $listing->images->first();
    $isFavorited = (bool) ($listing->is_favorited ?? false);
@endphp

<article class="min-w-0">
    <div class="relative overflow-hidden rounded-2xl bg-white shadow-sm">
        <a href="{{ route('listings.show', $listing) }}" class="block aspect-[3/4] overflow-hidden" aria-label="{{ $listing->title }}">
            @if ($image)
                <img src="{{ $image->url() }}" alt="{{ $image->alt_text ?: $listing->title }}" loading="lazy" class="h-full w-full object-cover transition duration-200 hover:scale-[1.02]">
            @else
                <div class="flex h-full items-end bg-gradient-to-br from-lilac via-sand to-coral/55 p-5">
                    <span class="text-4xl font-black tracking-[-0.06em] text-forest/75">{{ mb_substr($listing->title, 0, 1) }}</span>
                </div>
            @endif
        </a>

        @auth
            <form method="POST" action="{{ $isFavorited ? route('favorites.destroy', $listing) : route('favorites.store', $listing) }}" class="absolute right-3 top-3">
                @csrf
                @if ($isFavorited)
                    @method('DELETE')
                @endif
                <button class="flex size-10 items-center justify-center rounded-full bg-white/90 text-xl shadow-sm backdrop-blur" aria-label="{{ $isFavorited ? __('catalog.unfavorite') : __('catalog.favorite') }}">
                    <span aria-hidden="true">{{ $isFavorited ? '♥' : '♡' }}</span>
                </button>
            </form>
        @else
            <a href="{{ route('login') }}" class="absolute right-3 top-3 flex size-10 items-center justify-center rounded-full bg-white/90 text-xl shadow-sm backdrop-blur" aria-label="{{ __('catalog.favorite') }}">♡</a>
        @endauth
    </div>

    <div class="px-1 pt-3">
        <a href="{{ route('profiles.show', $listing->user) }}" class="text-xs font-semibold text-ink/50 hover:text-forest">{{ '@'.$listing->user->username }}</a>
        <h3 class="mt-1 truncate font-bold text-forest"><a href="{{ route('listings.show', $listing) }}">{{ $listing->title }}</a></h3>
        <p class="mt-1 truncate text-sm text-ink/55">
            {{ $listing->brand?->name ?? __('catalog.no_brand') }}
            @if ($listing->size) · {{ $listing->size }} @endif
            · {{ __('catalog.condition_labels.'.$listing->condition->value) }}
        </p>
        <x-money :amount="$listing->price_amount" :currency="$listing->currency" class="mt-2 block font-black text-forest" />
    </div>
</article>
