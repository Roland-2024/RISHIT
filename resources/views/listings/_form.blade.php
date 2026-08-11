@php($editing = isset($listing))

@if ($errors->any())
    <div class="mb-6 rounded-2xl border border-coral/35 bg-coral/10 p-4 text-sm text-coral-dark">
        <ul class="list-disc space-y-1 pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif

<form method="POST" action="{{ $editing ? route('listings.update', $listing) : route('listings.store') }}" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @if ($editing) @method('PUT') @endif

    <label class="block">
        <span class="mb-2 block font-bold">{{ __('catalog.form.title') }}</span>
        <input name="title" value="{{ old('title', $listing->title ?? '') }}" required maxlength="120" class="w-full rounded-xl border border-ink/15 bg-white px-4 py-3" placeholder="{{ __('catalog.form.title_hint') }}">
    </label>

    <label class="block">
        <span class="mb-2 block font-bold">{{ __('catalog.form.description') }}</span>
        <textarea name="description" required minlength="20" maxlength="5000" rows="7" class="w-full rounded-xl border border-ink/15 bg-white px-4 py-3" placeholder="{{ __('catalog.form.description_hint') }}">{{ old('description', $listing->description ?? '') }}</textarea>
    </label>

    <div class="grid gap-5 sm:grid-cols-2">
        <label>
            <span class="mb-2 block font-bold">{{ __('catalog.form.category') }}</span>
            <select name="category_id" required class="w-full rounded-xl border border-ink/15 bg-white px-4 py-3">
                <option value=""></option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected((string) old('category_id', $listing->category_id ?? '') === (string) $category->id)>{{ $category->label() }}</option>
                    @foreach ($category->children as $child)
                        <option value="{{ $child->id }}" @selected((string) old('category_id', $listing->category_id ?? '') === (string) $child->id)>— {{ $child->label() }}</option>
                    @endforeach
                @endforeach
            </select>
        </label>
        <label>
            <span class="mb-2 block font-bold">{{ __('catalog.form.brand') }}</span>
            <select name="brand_id" class="w-full rounded-xl border border-ink/15 bg-white px-4 py-3">
                <option value=""></option>
                @foreach ($brands as $brand)
                    <option value="{{ $brand->id }}" @selected((string) old('brand_id', $listing->brand_id ?? '') === (string) $brand->id)>{{ $brand->name }}</option>
                @endforeach
            </select>
        </label>
        <label>
            <span class="mb-2 block font-bold">{{ __('catalog.form.condition') }}</span>
            <select name="condition" required class="w-full rounded-xl border border-ink/15 bg-white px-4 py-3">
                <option value=""></option>
                @foreach ($conditions as $condition)
                    <option value="{{ $condition->value }}" @selected(old('condition', isset($listing) ? $listing->condition->value : '') === $condition->value)>{{ __('catalog.condition_labels.'.$condition->value) }}</option>
                @endforeach
            </select>
        </label>
        <label>
            <span class="mb-2 block font-bold">{{ __('catalog.form.size') }}</span>
            <input name="size" value="{{ old('size', $listing->size ?? '') }}" maxlength="40" class="w-full rounded-xl border border-ink/15 bg-white px-4 py-3">
        </label>
        <label>
            <span class="mb-2 block font-bold">{{ __('catalog.form.color') }}</span>
            <input name="color" value="{{ old('color', $listing->color ?? '') }}" maxlength="40" class="w-full rounded-xl border border-ink/15 bg-white px-4 py-3">
        </label>
        <label>
            <span class="mb-2 block font-bold">{{ __('catalog.form.price') }}</span>
            <span class="flex overflow-hidden rounded-xl border border-ink/15 bg-white focus-within:ring-2 focus-within:ring-forest/25">
                <input name="price" inputmode="decimal" value="{{ old('price', isset($listing) ? $listing->price()->decimal() : '') }}" required class="min-w-0 flex-1 border-0 px-4 py-3 focus:ring-0">
                <span class="flex items-center border-l border-ink/15 bg-sand px-4 text-sm font-black text-forest">EUR</span>
            </span>
        </label>
    </div>

    @if ($editing && $listing->images->isNotEmpty())
        <div class="grid grid-cols-4 gap-3">
            @foreach ($listing->images as $image)<img src="{{ $image->url() }}" alt="" class="aspect-square rounded-xl object-cover">@endforeach
        </div>
    @endif

    <label class="block rounded-2xl border border-dashed border-ink/25 bg-white/60 p-5">
        <span class="block font-bold">{{ __('catalog.form.photos') }}</span>
        <span class="mt-1 block text-sm text-ink/55">{{ $editing ? __('catalog.form.add_photos_hint') : __('catalog.form.photos_hint') }}</span>
        <input name="photos[]" type="file" accept="image/jpeg,image/png,image/webp" multiple @required(! $editing) class="mt-4 block w-full text-sm">
    </label>

    <button class="rounded-full bg-coral px-7 py-3.5 font-black text-white hover:bg-coral-dark">{{ $editing ? __('catalog.save') : __('catalog.create') }}</button>
</form>
