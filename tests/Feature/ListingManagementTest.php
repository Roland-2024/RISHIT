<?php

namespace Tests\Feature;

use App\Domain\Catalog\ListingStatus;
use App\Domain\Shared\Currency;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ListingManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_seller_can_create_edit_hide_and_delete_a_listing(): void
    {
        Storage::fake('public');
        $seller = User::factory()->create();
        $category = Category::factory()->create();
        $brand = Brand::factory()->create();

        $response = $this->actingAs($seller)->post('/en/items', [
            'title' => 'Warm wool coat',
            'description' => 'A warm wool coat with one small mark shown clearly in the photos.',
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'condition' => 'good',
            'size' => 'M',
            'color' => 'Camel',
            'price' => '1250.50',
            'currency' => 'ALL',
            'photos' => [UploadedFile::fake()->image('coat.jpg', 800, 1000)],
        ]);

        $listing = Listing::query()->sole();
        $response->assertRedirect('/en/items/'.$listing->slug);
        $this->assertSame(125050, $listing->price_amount);
        $this->assertSame(Currency::EUR, $listing->currency);
        Storage::disk('public')->assertExists($listing->images()->sole()->path);

        $this->put('/en/items/'.$listing->slug, [
            'title' => 'Warm wool coat updated',
            'description' => $listing->description,
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'condition' => 'very_good',
            'size' => 'M',
            'color' => 'Camel',
            'price' => '1300',
        ])->assertRedirect('/en/items/'.$listing->slug);

        $this->patch('/en/items/'.$listing->slug.'/visibility', ['status' => 'hidden'])->assertRedirect();
        $this->assertSame(ListingStatus::Hidden, $listing->fresh()->status);
        $this->get('/en/items/'.$listing->slug)->assertOk();

        $this->delete('/en/items/'.$listing->slug)->assertRedirect('/en/my/listings');
        $this->assertSoftDeleted($listing);
    }

    public function test_non_owner_cannot_manage_a_listing(): void
    {
        $listing = Listing::factory()->create();

        $this->actingAs(User::factory()->create())
            ->patch('/sq/items/'.$listing->slug.'/visibility', ['status' => 'hidden'])
            ->assertForbidden();
    }

    public function test_listing_price_must_be_greater_than_zero(): void
    {
        Storage::fake('public');
        $seller = User::factory()->create();

        $this->actingAs($seller)->post('/en/items', [
            'title' => 'Free coat',
            'description' => 'A coat described honestly but without a valid commerce price.',
            'category_id' => Category::factory()->create()->id,
            'condition' => 'good',
            'price' => '0.00',
            'photos' => [UploadedFile::fake()->image('coat.jpg', 800, 1000)],
        ])->assertInvalid(['price']);

        $this->assertDatabaseCount('listings', 0);
    }

    public function test_favorites_are_idempotent_and_private(): void
    {
        $user = User::factory()->create();
        $listing = Listing::factory()->create(['title' => 'Favorite jacket']);

        $this->actingAs($user)->post('/sq/favorites/'.$listing->slug)->assertRedirect();
        $this->post('/sq/favorites/'.$listing->slug)->assertRedirect();
        $this->assertDatabaseCount('favorites', 1);
        $this->get('/sq/favorites')->assertOk()->assertSee('Favorite jacket');

        $this->delete('/sq/favorites/'.$listing->slug)->assertRedirect();
        $this->assertDatabaseCount('favorites', 0);
        $this->post('/sq/logout');
        $this->get('/sq/favorites')->assertRedirect('/sq/login');
    }
}
