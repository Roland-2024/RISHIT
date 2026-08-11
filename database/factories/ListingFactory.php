<?php

namespace Database\Factories;

use App\Domain\Catalog\ListingCondition;
use App\Domain\Catalog\ListingStatus;
use App\Domain\Shared\Currency;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Listing> */
class ListingFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->words(3, true);

        return [
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'brand_id' => Brand::factory(),
            'slug' => Str::slug($title).'-'.Str::lower(Str::random(6)),
            'title' => ucfirst($title),
            'description' => fake()->paragraph(),
            'condition' => fake()->randomElement(ListingCondition::cases()),
            'size' => fake()->randomElement(['S', 'M', 'L', '40']),
            'color' => fake()->safeColorName(),
            'price_amount' => fake()->numberBetween(1000, 200000),
            'currency' => Currency::EUR,
            'status' => ListingStatus::Active,
        ];
    }
}
