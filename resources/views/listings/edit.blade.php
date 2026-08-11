<x-layout :title="__('catalog.edit_title').' — RISHIT'" :indexable="false">
    <div class="mx-auto max-w-3xl px-4 py-12 sm:px-6 lg:px-8">
        <h1 class="text-4xl font-black tracking-[-0.04em] text-forest">{{ __('catalog.edit_title') }}</h1>
        <div class="mt-8 rounded-3xl border border-ink/10 bg-white p-6 shadow-sm sm:p-8">
            @include('listings._form')
        </div>
    </div>
</x-layout>
