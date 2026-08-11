<?php

namespace App\Http\Controllers;

use App\Domain\Catalog\ListingCondition;
use App\Domain\Catalog\ListingImageStorage;
use App\Domain\Catalog\ListingStatus;
use App\Domain\Shared\Currency;
use App\Domain\Shared\Money;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Listing;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

class ListingController extends Controller
{
    public function show(string $locale, Listing $listing): View
    {
        abort_if(
            ! $listing->isPubliclyVisible() && auth()->id() !== $listing->user_id,
            404
        );

        $listing->load(['brand', 'category.parent', 'images', 'user'])
            ->loadExists(['favoritedBy as is_favorited' => fn ($query) => $query->whereKey(auth()->id() ?? 0)]);

        return view('listings.show', compact('listing'));
    }

    public function create(): View
    {
        return view('listings.create', $this->formData());
    }

    public function store(Request $request, ListingImageStorage $imageStorage): RedirectResponse
    {
        $validated = $request->validate($this->rules());
        $storedPaths = [];

        try {
            $listing = DB::transaction(function () use ($request, $validated, $imageStorage, &$storedPaths): Listing {
                $listing = $request->user()->listings()->create($this->attributes($validated, Currency::EUR) + [
                    'slug' => Str::slug($validated['title']).'-'.Str::lower(Str::random(8)),
                    'status' => ListingStatus::Active,
                ]);

                foreach ($request->file('photos', []) as $order => $photo) {
                    $storedPaths[] = $path = $imageStorage->store($photo);
                    $listing->images()->create(['path' => $path, 'alt_text' => $listing->title, 'sort_order' => $order]);
                }

                return $listing;
            });
        } catch (Throwable $exception) {
            $imageStorage->delete($storedPaths);
            throw $exception;
        }

        return redirect()->route('listings.show', $listing)->with('status', __('catalog.flash.created'));
    }

    public function edit(string $locale, Listing $listing): View
    {
        Gate::authorize('update', $listing);

        return view('listings.edit', $this->formData() + compact('listing'));
    }

    public function update(Request $request, string $locale, Listing $listing, ListingImageStorage $imageStorage): RedirectResponse
    {
        Gate::authorize('update', $listing);
        $validated = $request->validate($this->rules($listing));
        $newPhotos = $request->file('photos', []);

        if ($listing->images()->count() + count($newPhotos) > ListingImageStorage::MAX_FILES) {
            throw ValidationException::withMessages(['photos' => __('catalog.validation.photo_limit')]);
        }

        $storedPaths = [];

        try {
            DB::transaction(function () use ($listing, $validated, $newPhotos, $imageStorage, &$storedPaths): void {
                $listing->update($this->attributes($validated, $listing->currency));
                $nextOrder = ($listing->images()->max('sort_order') ?? -1) + 1;

                foreach ($newPhotos as $offset => $photo) {
                    $storedPaths[] = $path = $imageStorage->store($photo);
                    $listing->images()->create(['path' => $path, 'alt_text' => $listing->title, 'sort_order' => $nextOrder + $offset]);
                }
            });
        } catch (Throwable $exception) {
            $imageStorage->delete($storedPaths);
            throw $exception;
        }

        return redirect()->route('listings.show', $listing)->with('status', __('catalog.flash.updated'));
    }

    public function destroy(string $locale, Listing $listing, ListingImageStorage $imageStorage): RedirectResponse
    {
        Gate::authorize('delete', $listing);
        $paths = $listing->images()->pluck('path')->all();

        DB::transaction(function () use ($listing): void {
            $listing->images()->delete();
            $listing->delete();
        });

        $imageStorage->delete($paths);

        return redirect()->route('my-listings.index')->with('status', __('catalog.flash.deleted'));
    }

    private function formData(): array
    {
        return [
            'categories' => Category::query()->with(['children' => fn ($query) => $query->where('is_active', true)])->whereNull('parent_id')->where('is_active', true)->orderBy('sort_order')->get(),
            'brands' => Brand::query()->orderBy('name')->get(),
            'conditions' => ListingCondition::cases(),
        ];
    }

    private function rules(?Listing $listing = null): array
    {
        return [
            'title' => ['required', 'string', 'max:120'],
            'description' => ['required', 'string', 'min:20', 'max:5000'],
            'category_id' => ['required', Rule::exists('categories', 'id')->where('is_active', true)],
            'brand_id' => ['nullable', Rule::exists('brands', 'id')],
            'condition' => ['required', Rule::enum(ListingCondition::class)],
            'size' => ['nullable', 'string', 'max:40'],
            'color' => ['nullable', 'string', 'max:40'],
            'price' => [
                'bail',
                'required',
                'regex:/^\d{1,7}(?:[.,]\d{1,2})?$/',
                function (string $attribute, mixed $value, Closure $fail): void {
                    if (Money::fromDecimal((string) $value, Currency::EUR)->amount === 0) {
                        $fail(__('catalog.validation.positive_price'));
                    }
                },
            ],
            'photos' => [$listing ? 'nullable' : 'required', 'array', 'max:'.ListingImageStorage::MAX_FILES],
            'photos.*' => [
                'bail',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:'.ListingImageStorage::MAX_KILOBYTES,
                'dimensions:min_width='.ListingImageStorage::MIN_DIMENSION
                    .',min_height='.ListingImageStorage::MIN_DIMENSION
                    .',max_width='.ListingImageStorage::MAX_DIMENSION
                    .',max_height='.ListingImageStorage::MAX_DIMENSION,
            ],
        ];
    }

    private function attributes(array $validated, Currency $currency): array
    {
        return Arr::except($validated, ['photos', 'price']) + [
            'price_amount' => Money::fromDecimal($validated['price'], $currency)->amount,
            'currency' => $currency,
        ];
    }
}
