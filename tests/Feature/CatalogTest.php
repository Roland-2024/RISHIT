<?php

namespace Tests\Feature;

use App\Domain\Catalog\ListingCondition;
use App\Domain\Catalog\ListingStatus;
use App\Domain\Shared\Currency;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_search_filters_visible_items_and_renders_detail_and_profile_pages(): void
    {
        [$listing, $hidden, $legacy] = $this->catalogFixtures();

        $this->get('/en')->assertOk()->assertSee($listing->title)->assertDontSee($hidden->title)->assertDontSee($legacy->title);
        $this->get('/en/catalog?q=Linen&brand=mango&condition=very_good&currency=EUR')
            ->assertOk()
            ->assertSee($listing->title)
            ->assertDontSee($hidden->title)
            ->assertDontSee($legacy->title);
        $this->get('/en/items/'.$listing->slug)->assertOk()->assertSee($listing->description);
        $this->get('/en/members/'.$listing->user->username)->assertOk()->assertSee($listing->title);
        $this->get('/en/items/'.$hidden->slug)->assertNotFound();
        $this->get('/en/items/'.$legacy->slug)->assertNotFound();
    }

    public function test_versioned_api_exposes_the_same_visible_catalog(): void
    {
        [$listing, $hidden, $legacy] = $this->catalogFixtures();

        $this->getJson('/api/v1/listings?q=Linen&currency=EUR')
            ->assertOk()
            ->assertJsonPath('data.0.slug', $listing->slug)
            ->assertJsonMissing(['slug' => $hidden->slug])
            ->assertJsonMissing(['slug' => $legacy->slug]);

        $this->getJson('/api/v1/listings?currency=ALL')->assertUnprocessable();

        $this->getJson('/api/v1/listings/'.$listing->slug)
            ->assertOk()
            ->assertJsonPath('data.price.amount', 125050)
            ->assertJsonPath('data.seller.username', $listing->user->username);
    }

    private function catalogFixtures(): array
    {
        $seller = User::factory()->create(['username' => 'ana_style']);
        $category = Category::factory()->create(['slug' => 'outerwear', 'name_en' => 'Outerwear']);
        $brand = Brand::factory()->create(['slug' => 'mango', 'name' => 'Mango']);
        $listing = Listing::factory()->for($seller)->for($category)->for($brand)->create([
            'slug' => 'linen-blazer',
            'title' => 'Linen blazer',
            'description' => 'A carefully kept linen blazer with light natural wear.',
            'condition' => ListingCondition::VeryGood,
            'currency' => Currency::EUR,
            'price_amount' => 125050,
        ]);
        $hidden = Listing::factory()->for($seller)->for($category)->create([
            'title' => 'Hidden coat',
            'status' => ListingStatus::Hidden,
        ]);
        $legacy = Listing::factory()->for($seller)->for($category)->create([
            'title' => 'Legacy lek coat',
            'currency' => Currency::ALL,
            'status' => ListingStatus::Active,
        ]);

        return [$listing, $hidden, $legacy];
    }
}
