<x-layout :title="__('catalog.favorites_title').' — RISHIT'" :indexable="false">
    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <h1 class="text-4xl font-black tracking-[-0.04em] text-forest">{{ __('catalog.favorites_title') }}</h1>
        @if ($listings->isEmpty())
            <p class="mt-8 rounded-3xl border border-dashed border-ink/20 p-10 text-center font-bold text-forest">{{ __('catalog.favorites_empty') }}</p>
        @else
            <div class="mt-8 grid grid-cols-2 gap-x-4 gap-y-10 sm:grid-cols-3 lg:grid-cols-4">
                @foreach ($listings as $listing)<x-listing-card :listing="$listing" />@endforeach
            </div>
            <div class="mt-12">{{ $listings->links() }}</div>
        @endif
    </div>
</x-layout>
