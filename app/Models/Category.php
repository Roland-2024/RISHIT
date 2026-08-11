<?php

namespace App\Models;

use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['parent_id', 'slug', 'name_sq', 'name_en', 'sort_order', 'is_active'])]
class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
    use HasFactory;

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order');
    }

    public function listings(): HasMany
    {
        return $this->hasMany(Listing::class);
    }

    public function label(): string
    {
        return $this->labels()[app()->getLocale()];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** @return array{sq: string, en: string} */
    public function labels(): array
    {
        return ['sq' => $this->name_sq, 'en' => $this->name_en];
    }

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
}
