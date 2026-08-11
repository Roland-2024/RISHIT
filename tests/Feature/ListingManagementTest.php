<?php

namespace Tests\Feature;

use App\Domain\Catalog\ListingStatus;
use App\Domain\Shared\Currency;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Listing;
use App\Models\ListingImage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
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
        $imagePath = $listing->images()->sole()->path;
        Storage::disk('public')->assertExists($imagePath);

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
        $this->assertDatabaseCount('listing_images', 0);
        Storage::disk('public')->assertMissing($imagePath);
    }

    public function test_non_owner_cannot_manage_a_listing(): void
    {
        Storage::fake('public');
        $listing = Listing::factory()->create();
        $attacker = User::factory()->create();

        $this->actingAs($attacker)
            ->patch('/sq/items/'.$listing->slug.'/visibility', ['status' => 'hidden'])
            ->assertForbidden();

        $this->put('/sq/items/'.$listing->slug, [
            'title' => 'Unauthorized replacement',
            'description' => 'This valid-looking update must be rejected before its image is written.',
            'category_id' => $listing->category_id,
            'condition' => 'good',
            'price' => '50',
            'photos' => [UploadedFile::fake()->image('replacement.jpg', 800, 1000)],
        ])->assertForbidden();

        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_images_are_reencoded_without_embedded_trailing_data_in_upload_order(): void
    {
        config(['marketplace.listing_image_disk' => 'listing-images']);
        Storage::fake('listing-images');
        $seller = User::factory()->create();
        $category = Category::factory()->create();
        $first = UploadedFile::fake()->image('first.jpg', 800, 1000);
        $second = UploadedFile::fake()->image('second.png', 1000, 800);
        file_put_contents($first->getRealPath(), 'private-camera-metadata', FILE_APPEND);

        $this->actingAs($seller)->post('/en/items', [
            'title' => 'Safely processed coat',
            'description' => 'A complete listing description for testing safe image processing.',
            'category_id' => $category->id,
            'condition' => 'good',
            'price' => '50',
            'photos' => [$first, $second],
        ])->assertRedirect();

        $images = Listing::query()->sole()->images;
        $this->assertSame([0, 1], $images->pluck('sort_order')->all());
        $this->assertStringEndsWith('.jpg', $images[0]->path);
        $this->assertStringEndsWith('.png', $images[1]->path);

        $stored = Storage::disk('listing-images')->get($images[0]->path);
        $this->assertStringNotContainsString('private-camera-metadata', $stored);
        $this->assertSame('image/jpeg', getimagesizefromstring($stored)['mime']);
    }

    public function test_invalid_and_oversized_dimension_uploads_are_rejected(): void
    {
        Storage::fake('public');
        $seller = User::factory()->create();
        $category = Category::factory()->create();
        $payload = [
            'title' => 'Rejected image listing',
            'description' => 'A complete listing description whose image should be rejected.',
            'category_id' => $category->id,
            'condition' => 'good',
            'price' => '50',
        ];

        $this->actingAs($seller)->post('/en/items', $payload + [
            'photos' => [UploadedFile::fake()->createWithContent('deceptive.jpg', '<?php echo "not an image";')],
        ])->assertInvalid(['photos.0']);

        $this->post('/en/items', $payload + [
            'photos' => [UploadedFile::fake()->image('too-wide.jpg', 4097, 320)],
        ])->assertInvalid(['photos.0']);

        $this->assertDatabaseCount('listings', 0);
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_files_are_cleaned_up_when_an_image_database_write_fails(): void
    {
        Storage::fake('public');
        config(['marketplace.fail_second_image_for_test' => true]);
        ListingImage::creating(function (ListingImage $image): void {
            if (config('marketplace.fail_second_image_for_test') && $image->sort_order === 1) {
                throw new RuntimeException('Simulated image database failure.');
            }
        });

        try {
            $this->withoutExceptionHandling()->actingAs(User::factory()->create())->post('/en/items', [
                'title' => 'Rollback image listing',
                'description' => 'A complete listing description used to verify orphan cleanup.',
                'category_id' => Category::factory()->create()->id,
                'condition' => 'good',
                'price' => '50',
                'photos' => [
                    UploadedFile::fake()->image('first.jpg', 800, 1000),
                    UploadedFile::fake()->image('second.jpg', 800, 1000),
                ],
            ]);

            $this->fail('Expected the simulated image write to fail.');
        } catch (RuntimeException $exception) {
            $this->assertSame('Simulated image database failure.', $exception->getMessage());
        } finally {
            config(['marketplace.fail_second_image_for_test' => false]);
        }

        $this->assertDatabaseCount('listings', 0);
        $this->assertDatabaseCount('listing_images', 0);
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_deleting_an_image_removes_its_stored_file(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('listings/deleted.jpg', 'contents');
        $image = Listing::factory()->create()->images()->create([
            'path' => 'listings/deleted.jpg',
            'sort_order' => 0,
        ]);

        $image->delete();

        Storage::disk('public')->assertMissing('listings/deleted.jpg');
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
