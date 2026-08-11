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
        [$listing, $hidden, $legacy, $sold, $deleted] = $this->catalogFixtures();

        $this->get('/en')->assertOk()->assertSee($listing->title)->assertDontSee($hidden->title)->assertDontSee($legacy->title)->assertDontSee($sold->title)->assertDontSee($deleted->title);
        $this->get('/en/catalog?q=Linen&brand=mango&condition=very_good&currency=EUR')
            ->assertOk()
            ->assertSee($listing->title)
            ->assertDontSee($hidden->title)
            ->assertDontSee($legacy->title)
            ->assertDontSee($sold->title)
            ->assertDontSee($deleted->title);
        $this->get('/en/items/'.$listing->slug)->assertOk()->assertSee($listing->description);
        $this->get('/en/members/'.$listing->user->username)->assertOk()->assertSee($listing->title);
        $this->get('/en/items/'.$hidden->slug)->assertNotFound();
        $this->get('/en/items/'.$legacy->slug)->assertNotFound();
        $this->get('/en/items/'.$sold->slug)->assertNotFound();
        $this->get('/en/items/'.$deleted->slug)->assertNotFound();
    }

    public function test_category_and_brand_landings_are_server_rendered_and_use_the_visible_catalog(): void
    {
        [$listing, $hidden, $legacy, $sold, $deleted] = $this->catalogFixtures();

        $this->get('/en/categories/outerwear')
            ->assertOk()
            ->assertSee('<link rel="canonical" href="'.config('app.url').'/en/categories/outerwear">', false)
            ->assertSee('<h1', false)
            ->assertSee('Outerwear')
            ->assertSee($listing->title)
            ->assertDontSee($hidden->title)
            ->assertDontSee($legacy->title)
            ->assertDontSee($sold->title)
            ->assertDontSee($deleted->title);

        $this->get('/sq/brands/mango')
            ->assertOk()
            ->assertSee('<link rel="alternate" hreflang="en" href="'.config('app.url').'/en/brands/mango">', false)
            ->assertSee('Mango')
            ->assertSee($listing->title)
            ->assertDontSee($hidden->title)
            ->assertDontSee($legacy->title)
            ->assertDontSee($sold->title)
            ->assertDontSee($deleted->title);
    }

    public function test_versioned_api_exposes_the_same_visible_catalog(): void
    {
        [$listing, $hidden, $legacy, $sold, $deleted] = $this->catalogFixtures();

        $this->getJson('/api/v1/listings?q=Linen&currency=EUR')
            ->assertOk()
            ->assertJsonPath('data.0.slug', $listing->slug)
            ->assertJsonMissing(['slug' => $hidden->slug])
            ->assertJsonMissing(['slug' => $legacy->slug])
            ->assertJsonMissing(['slug' => $sold->slug])
            ->assertJsonMissing(['slug' => $deleted->slug]);

        $this->getJson('/api/v1/listings?currency=ALL')->assertUnprocessable();

        $this->getJson('/api/v1/listings/'.$listing->slug)
            ->assertOk()
            ->assertJsonPath('data.price.amount', 125050)
            ->assertJsonPath('data.category.labels.sq', 'Veshje të jashtme')
            ->assertJsonPath('data.category.labels.en', 'Outerwear')
            ->assertJsonPath('data.seller.username', $listing->user->username);

        $this->getJson('/api/v1/listings/'.$sold->slug)->assertNotFound();
        $this->getJson('/api/v1/listings/'.$deleted->slug)->assertNotFound();
    }

    public function test_owner_sees_why_private_inventory_is_unavailable(): void
    {
        [, $hidden, $legacy, $sold] = $this->catalogFixtures();

        $this->actingAs($hidden->user)
            ->get('/en/items/'.$hidden->slug)
            ->assertOk()
            ->assertSee(trans('catalog.unavailable.hidden', locale: 'en'))
            ->assertDontSee(trans('catalog.buy_unavailable', locale: 'en'));

        $this->get('/en/items/'.$sold->slug)
            ->assertOk()
            ->assertSee(trans('catalog.unavailable.sold', locale: 'en'));

        $this->get('/en/items/'.$legacy->slug)
            ->assertOk()
            ->assertSee(trans('catalog.unavailable.legacy_currency', locale: 'en'));
    }

    private function catalogFixtures(): array
    {
        $seller = User::factory()->create(['username' => 'ana_style']);
        $category = Category::factory()->create([
            'slug' => 'outerwear',
            'name_sq' => 'Veshje të jashtme',
            'name_en' => 'Outerwear',
        ]);
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
        $sold = Listing::factory()->for($seller)->for($category)->create([
            'title' => 'Sold coat',
            'status' => ListingStatus::Sold,
        ]);
        $deleted = Listing::factory()->for($seller)->for($category)->create(['title' => 'Deleted coat']);
        $deleted->delete();

        return [$listing, $hidden, $legacy, $sold, $deleted];
    }
}
