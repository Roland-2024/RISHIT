<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_with_one_buyer_and_seller_account(): void
    {
        $response = $this->post('/sq/register', [
            'name' => 'Ada Test',
            'username' => 'Ada_Test',
            'email' => 'ADA@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/sq');
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'username' => 'ada_test',
            'email' => 'ada@example.com',
            'preferred_locale' => 'sq',
            'preferred_currency' => 'EUR',
        ]);
    }

    public function test_user_can_log_in_and_log_out(): void
    {
        $user = User::factory()->create(['password' => 'password123']);

        $this->post('/en/login', [
            'email' => $user->email,
            'password' => 'password123',
        ])->assertRedirect('/en');

        $this->assertAuthenticatedAs($user);

        $this->post('/en/logout')->assertRedirect('/en');
        $this->assertGuest();
    }
}
