<?php

namespace Tests\Feature;

use App\Models\User;

class AuthenticationTest extends InventoryTestCase
{
    public function test_guest_cannot_open_dashboard(): void
    {
        $this->get('/dashboard')->assertRedirect(route('login'));
    }

    public function test_wrong_password_is_rejected(): void
    {
        $user = $this->makeUser();

        $this->post(route('login.post'), ['email' => $user->email, 'password' => 'salah'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_valid_login_reaches_dashboard(): void
    {
        $user = $this->makeUser(User::ROLE_ADMIN);

        $this->post(route('login.post'), ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
        $this->actingAs($user)->get(route('dashboard'))->assertOk();
    }
}