<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ListingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->title,
            'description' => $this->description,
            'condition' => $this->condition->value,
            'size' => $this->size,
            'color' => $this->color,
            'price' => [
                'amount' => $this->price_amount,
                'currency' => $this->currency->value,
                'formatted' => $this->price()->format(),
            ],
            'category' => [
                'slug' => $this->category->slug,
                'name' => $this->category->label(),
                'labels' => $this->category->labels(),
            ],
            'brand' => $this->brand ? ['slug' => $this->brand->slug, 'name' => $this->brand->name] : null,
            'seller' => ['username' => $this->user->username, 'name' => $this->user->name],
            'images' => $this->images->map(fn ($image) => [
                'url' => $image->url(),
                'alt' => $image->alt_text,
            ]),
            'created_at' => $this->created_at,
        ];
    }
}
