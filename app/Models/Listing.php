<?php

namespace App\Models;

use App\Domain\Catalog\ListingCondition;
use App\Domain\Catalog\ListingStatus;
use App\Domain\Shared\Currency;
use App\Domain\Shared\Money;
use Database\Factories\ListingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['user_id', 'category_id', 'brand_id', 'slug', 'title', 'description', 'condition', 'size', 'color', 'price_amount', 'currency', 'status'])]
class Listing extends Model
{
    /** @use HasFactory<ListingFactory> */
    use HasFactory, SoftDeletes;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ListingImage::class)->orderBy('sort_order');
    }

    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    public function price(): Money
    {
        return new Money($this->price_amount, $this->currency);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** @param Builder<Listing> $query */
    public function scopeVisible(Builder $query): void
    {
        $query->where('status', ListingStatus::Active)
            ->where('currency', Currency::EUR);
    }

    /** @param Builder<Listing> $query */
    public function scopeWithCardData(Builder $query, ?int $viewerId = null): void
    {
        $query->with(['brand', 'category', 'images', 'user'])
            ->when($viewerId, fn (Builder $query, int $id) => $query->withExists([
                'favoritedBy as is_favorited' => fn (Builder $query) => $query->whereKey($id),
            ]));
    }

    /** @param Builder<Listing> $query */
    public function scopeFilter(Builder $query, array $filters): void
    {
        $query
            ->when($filters['q'] ?? null, function (Builder $query, string $search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($filters['category'] ?? null, function (Builder $query, string $slug): void {
                $query->whereHas('category', fn (Builder $query) => $query
                    ->where('slug', $slug)
                    ->orWhereHas('parent', fn (Builder $query) => $query->where('slug', $slug)));
            })
            ->when($filters['brand'] ?? null, fn (Builder $query, string $slug) => $query
                ->whereHas('brand', fn (Builder $query) => $query->where('slug', $slug)))
            ->when($filters['condition'] ?? null, fn (Builder $query, string $condition) => $query
                ->where('condition', $condition))
            ->when($filters['currency'] ?? null, fn (Builder $query, string $currency) => $query
                ->where('currency', $currency))
            ->when($filters['min_price'] ?? null, fn (Builder $query, string $price) => $query
                ->where('price_amount', '>=', Money::fromDecimal($price, Currency::from($filters['currency'] ?? config('marketplace.default_currency')))->amount))
            ->when($filters['max_price'] ?? null, fn (Builder $query, string $price) => $query
                ->where('price_amount', '<=', Money::fromDecimal($price, Currency::from($filters['currency'] ?? config('marketplace.default_currency')))->amount));

        match ($filters['sort'] ?? 'newest') {
            'price_asc' => $query->orderBy('price_amount'),
            'price_desc' => $query->orderByDesc('price_amount'),
            default => $query->latest(),
        };
    }

    protected function casts(): array
    {
        return [
            'condition' => ListingCondition::class,
            'currency' => Currency::class,
            'price_amount' => 'integer',
            'status' => ListingStatus::class,
        ];
    }
}
