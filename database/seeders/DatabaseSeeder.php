<?php

namespace Database\Seeders;

use App\Domain\Catalog\ListingCondition;
use App\Domain\Catalog\ListingStatus;
use App\Domain\Shared\Currency;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $seller = User::query()->updateOrCreate(['email' => 'ana@rishit.test'], [
            'name' => 'Ana Kola',
            'username' => 'ana_style',
            'password' => 'password',
            'preferred_locale' => 'sq',
            'preferred_currency' => 'EUR',
        ]);

        $roots = collect([
            ['slug' => 'women', 'name_sq' => 'Gra', 'name_en' => 'Women'],
            ['slug' => 'men', 'name_sq' => 'Burra', 'name_en' => 'Men'],
            ['slug' => 'kids', 'name_sq' => 'Fëmijë', 'name_en' => 'Kids'],
            ['slug' => 'accessories', 'name_sq' => 'Aksesorë', 'name_en' => 'Accessories'],
        ])->mapWithKeys(function (array $category, int $order): array {
            $model = Category::query()->updateOrCreate(['slug' => $category['slug']], $category + [
                'sort_order' => $order,
                'is_active' => true,
            ]);

            return [$model->slug => $model];
        });

        $children = collect([
            ['women', 'dresses', 'Fustane', 'Dresses'],
            ['women', 'tops', 'Bluza', 'Tops'],
            ['women', 'outerwear', 'Xhaketa dhe pallto', 'Outerwear'],
            ['men', 'shirts', 'Këmisha', 'Shirts'],
            ['men', 'trousers', 'Pantallona', 'Trousers'],
            ['men', 'sneakers', 'Atlete', 'Sneakers'],
            ['kids', 'kids-clothing', 'Veshje për fëmijë', 'Kids clothing'],
            ['accessories', 'bags', 'Çanta', 'Bags'],
            ['accessories', 'jewellery', 'Bizhuteri', 'Jewellery'],
        ])->mapWithKeys(function (array $category, int $order) use ($roots): array {
            $model = Category::query()->updateOrCreate(['slug' => $category[1]], [
                'parent_id' => $roots[$category[0]]->id,
                'name_sq' => $category[2],
                'name_en' => $category[3],
                'sort_order' => $order,
                'is_active' => true,
            ]);

            return [$model->slug => $model];
        });

        $brands = collect(['Adidas', 'Levi’s', 'Mango', 'Nike', 'Zara'])
            ->mapWithKeys(function (string $name): array {
                $brand = Brand::query()->updateOrCreate(['name' => $name], ['slug' => (string) str($name)->ascii()->slug()]);

                return [$brand->name => $brand];
            });

        $listings = [
            ['linen-blazer-beige', 'Xhaketë lino bezhë', 'outerwear', 'Mango', ListingCondition::VeryGood, 'M', 'Bezhë', 5200],
            ['red-midi-dress', 'Fustan midi në të kuqe', 'dresses', 'Zara', ListingCondition::Good, 'S', 'E kuqe', 3800],
            ['classic-denim-jacket', 'Xhaketë klasike xhins', 'outerwear', 'Levi’s', ListingCondition::VeryGood, 'L', 'Blu', 6500],
            ['white-running-sneakers', 'Atlete të bardha vrapimi', 'sneakers', 'Nike', ListingCondition::Good, '42', 'E bardhë', 4900],
            ['soft-knit-top', 'Bluzë e butë e thurur', 'tops', 'Mango', ListingCondition::NewWithoutTags, 'M', 'Krem', 2900],
            ['leather-crossbody-bag', 'Çantë e vogël lëkure', 'bags', null, ListingCondition::VeryGood, null, 'Kafe', 7100],
            ['striped-cotton-shirt', 'Këmishë pambuku me vija', 'shirts', 'Zara', ListingCondition::Good, 'L', 'Blu e bardhë', 3100],
            ['kids-rain-jacket', 'Xhaketë shiu për fëmijë', 'kids-clothing', 'Adidas', ListingCondition::VeryGood, '8 vjeç', 'E verdhë', 3400],
        ];

        foreach ($listings as [$slug, $title, $category, $brand, $condition, $size, $color, $price]) {
            Listing::query()->updateOrCreate(['slug' => $slug], [
                'user_id' => $seller->id,
                'category_id' => $children[$category]->id,
                'brand_id' => $brand ? $brands[$brand]->id : null,
                'title' => $title,
                'description' => 'Artikull i ruajtur me kujdes. Gjendja, masa dhe çdo shenjë përdorimi janë përshkruar me ndershmëri.',
                'condition' => $condition,
                'size' => $size,
                'color' => $color,
                'price_amount' => $price,
                'currency' => Currency::EUR,
                'status' => ListingStatus::Active,
            ]);
        }
    }
}
