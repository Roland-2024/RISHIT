<?php

namespace App\Models;

use App\Domain\Catalog\ListingImageStorage;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['path', 'alt_text', 'sort_order'])]
class ListingImage extends Model
{
    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    public function url(): string
    {
        return app(ListingImageStorage::class)->url($this->path);
    }

    protected static function booted(): void
    {
        static::deleted(fn (ListingImage $image) => app(ListingImageStorage::class)->delete($image->path));
    }
}
