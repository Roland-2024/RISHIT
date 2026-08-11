<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocalizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_root_redirects_to_the_albanian_marketplace(): void
    {
        $this->get('/')->assertRedirect('/sq');
    }

    public function test_public_homepage_is_server_rendered_in_both_languages(): void
    {
        $this->get('/sq')
            ->assertOk()
            ->assertSee('lang="sq"', false)
            ->assertSee(trans('ui.hero.fee_promise', locale: 'sq'))
            ->assertSee(trans('ui.hero.title', locale: 'sq'));

        $this->get('/en')
            ->assertOk()
            ->assertSee('lang="en"', false)
            ->assertSee(trans('ui.hero.fee_promise', locale: 'en'))
            ->assertSee(trans('ui.hero.title', locale: 'en'));
    }

    public function test_unsupported_locale_returns_not_found(): void
    {
        $this->get('/de')->assertNotFound();
    }

    public function test_both_languages_use_the_same_euro_business_currency(): void
    {
        $user = User::factory()->create();

        $this->assertSame(['sq', 'en'], config('marketplace.locales'));
        $this->assertSame('EUR', config('marketplace.default_currency'));
        $this->actingAs($user)->get('/sq/sell')->assertOk()->assertSee('EUR')->assertDontSee('name="currency"', false);
        $this->get('/en/sell')->assertOk()->assertSee('EUR')->assertDontSee('name="currency"', false);
        $this->post('/en/preferences/currency', ['currency' => 'ALL'])->assertNotFound();
    }
}
