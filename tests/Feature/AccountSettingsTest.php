<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_manage_profile_and_albania_addresses(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->put('/en/settings/profile', [
            'name' => 'Ada Updated',
            'username' => 'ADA_UPDATED',
            'email' => 'ADA.UPDATED@example.com',
            'preferred_locale' => 'en',
        ])->assertRedirect('/en/settings');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Ada Updated',
            'username' => 'ada_updated',
            'email' => 'ada.updated@example.com',
            'preferred_locale' => 'en',
        ]);
        $this->assertNull($user->fresh()->email_verified_at);

        $this->post('/en/settings/addresses', $this->addressData() + ['country_code' => 'XK'])
            ->assertRedirect('/en/settings');

        $address = Address::query()->sole();
        $this->assertSame('AL', $address->country_code);

        $this->put('/en/settings/addresses/'.$address->id, $this->addressData(['city' => 'Durrës']))
            ->assertRedirect('/en/settings');
        $this->assertSame('Durrës', $address->fresh()->city);

        $this->delete('/en/settings/addresses/'.$address->id)->assertRedirect('/en/settings');
        $this->assertDatabaseCount('addresses', 0);
    }

    public function test_profile_and_address_validation_is_enforced(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        $this->actingAs($user)->put('/sq/settings/profile', [
            'name' => '',
            'username' => $other->username,
            'email' => $other->email,
            'preferred_locale' => 'de',
        ])->assertSessionHasErrors(['name', 'username', 'email', 'preferred_locale']);

        $this->post('/sq/settings/addresses', $this->addressData([
            'phone' => '069 123 4567',
            'postal_code' => '12345',
        ]))->assertSessionHasErrors(['phone', 'postal_code']);

        $this->assertDatabaseCount('addresses', 0);
    }

    public function test_addresses_are_private_to_their_owner(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $address = $owner->addresses()->create($this->addressData());

        $this->actingAs($other)
            ->put('/en/settings/addresses/'.$address->id, $this->addressData(['street' => 'Changed street']))
            ->assertForbidden();
        $this->delete('/en/settings/addresses/'.$address->id)->assertForbidden();

        $this->get('/en/members/'.$owner->username)
            ->assertOk()
            ->assertDontSee($address->street)
            ->assertDontSee($address->phone);
        $this->assertDatabaseHas('addresses', ['id' => $address->id, 'street' => $address->street]);
    }

    public function test_settings_require_authentication_and_render_in_both_languages(): void
    {
        $this->get('/sq/settings')->assertRedirect('/sq/login');

        $user = User::factory()->create();
        $this->actingAs($user)->get('/sq/settings')
            ->assertOk()
            ->assertSee(trans('account.title', locale: 'sq'));
        $this->get('/en/settings')
            ->assertOk()
            ->assertSee(trans('account.title', locale: 'en'));
    }

    private function addressData(array $overrides = []): array
    {
        return array_merge([
            'label' => 'Home',
            'recipient_name' => 'Ada Test',
            'phone' => '+355691234567',
            'street' => 'Rruga e Kavajës 10',
            'city' => 'Tiranë',
            'postal_code' => '1001',
        ], $overrides);
    }
}
