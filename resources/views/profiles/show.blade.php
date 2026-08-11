<x-layout :title="$seller->username.' — RISHIT'" :description="__('catalog.seller_items', ['username' => $seller->username])">
    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <section class="rounded-3xl bg-forest p-8 text-sand sm:p-10">
            <p class="text-sm font-bold text-lilac">{{ __('catalog.seller') }}</p>
            <h1 class="mt-2 text-4xl font-black tracking-[-0.04em]">{{ '@'.$seller->username }}</h1>
            <p class="mt-3 text-sand/65">{{ __('catalog.member_since', ['date' => $seller->created_at->translatedFormat('F Y')]) }}</p>
            @auth
                @if (auth()->user()->is($seller))
                    <div class="mt-5 flex flex-wrap gap-3">
                        <a href="{{ route('my-listings.index') }}" class="inline-block rounded-full bg-sand px-5 py-2.5 font-bold text-forest">{{ __('catalog.my_listings') }}</a>
                        <a href="{{ route('account.edit') }}" class="inline-block rounded-full border border-sand/40 px-5 py-2.5 font-bold text-sand">{{ __('ui.nav.account') }}</a>
                    </div>
                @endif
            @endauth
        </section>

        <h2 class="mt-10 text-2xl font-black text-forest">{{ __('catalog.seller_items', ['username' => $seller->username]) }}</h2>
        @if ($listings->isEmpty())
            <p class="mt-6 rounded-3xl border border-dashed border-ink/20 p-10 text-center">{{ __('catalog.empty') }}</p>
        @else
            <div class="mt-8 grid grid-cols-2 gap-x-4 gap-y-10 sm:grid-cols-3 lg:grid-cols-4">
                @foreach ($listings as $listing)<x-listing-card :listing="$listing" />@endforeach
            </div>
            <div class="mt-12">{{ $listings->links() }}</div>
        @endif
    </div>
</x-layout>
