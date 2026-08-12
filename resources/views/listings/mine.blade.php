<x-layout :title="__('catalog.my_listings').' — RISHIT'" :indexable="false">
    <div class="mx-auto max-w-5xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between gap-4">
            <h1 class="text-4xl font-black tracking-[-0.04em] text-forest">{{ __('catalog.my_listings') }}</h1>
            <a href="{{ route('listings.create') }}" class="rounded-full bg-coral px-5 py-3 font-bold text-white">{{ __('ui.nav.sell') }}</a>
        </div>

        @if ($listings->isEmpty())
            <p class="mt-8 rounded-3xl border border-dashed border-ink/20 p-10 text-center font-bold text-forest">{{ __('catalog.my_listings_empty') }}</p>
        @else
            <div class="mt-8 space-y-4">
                @foreach ($listings as $listing)
                    <article class="grid gap-5 rounded-2xl border border-ink/10 bg-white p-4 sm:grid-cols-[7rem_1fr_auto] sm:items-center">
                        @if ($listing->images->first())
                            <img src="{{ $listing->images->first()->url() }}" alt="" class="aspect-square w-28 rounded-xl object-cover">
                        @else
                            <div class="flex aspect-square w-28 items-end rounded-xl bg-lilac p-3 text-3xl font-black text-forest">{{ mb_substr($listing->title, 0, 1) }}</div>
                        @endif
                        <div>
                            <p class="text-xs font-black uppercase tracking-wider text-coral">{{ __('catalog.status_labels.'.$listing->status->value) }}</p>
                            <h2 class="mt-1 text-xl font-black text-forest"><a href="{{ route('listings.show', $listing) }}">{{ $listing->title }}</a></h2>
                            <x-money :amount="$listing->price_amount" :currency="$listing->currency" class="mt-2 block font-bold" />
                        </div>
                        <div class="flex flex-wrap gap-2 sm:justify-end">
                            @if ($listing->status !== App\Domain\Catalog\ListingStatus::Reserved)
                                <a href="{{ route('listings.edit', $listing) }}" class="rounded-full border border-ink/15 px-4 py-2 text-sm font-bold">{{ __('catalog.edit') }}</a>
                                @if ($listing->status !== App\Domain\Catalog\ListingStatus::Sold)
                                    <form method="POST" action="{{ route('listings.visibility', $listing) }}">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="{{ $listing->status->value === 'active' ? 'hidden' : 'active' }}">
                                        <button class="rounded-full border border-ink/15 px-4 py-2 text-sm font-bold">{{ $listing->status->value === 'active' ? __('catalog.hide') : __('catalog.unhide') }}</button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('listings.destroy', $listing) }}" onsubmit="return confirm(@js(__('catalog.delete_confirm')))">
                                    @csrf @method('DELETE')
                                    <button class="rounded-full px-4 py-2 text-sm font-bold text-coral-dark">{{ __('catalog.delete') }}</button>
                                </form>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
            <div class="mt-12">{{ $listings->links() }}</div>
        @endif
    </div>
</x-layout>
