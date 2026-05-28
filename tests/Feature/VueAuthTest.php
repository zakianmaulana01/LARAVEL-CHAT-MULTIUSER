<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VueAuthTest extends TestCase
{
    use RefreshDatabase;

    // ─── Login JSON API ───────────────────────────────────────────────────────

    public function test_vue_login_berhasil_dengan_kredensial_valid(): void
    {
        $user = User::factory()->create([
            'email'    => 'vue@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/vue/login', [
            'email'    => 'vue@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'user' => ['id', 'name', 'email', 'role'],
                'message',
            ]);

        $this->assertAuthenticatedAs($user);
    }

    public function test_vue_login_gagal_dengan_kredensial_salah(): void
    {
        User::factory()->create(['email' => 'vue@example.com']);

        $response = $this->postJson('/vue/login', [
            'email'    => 'vue@example.com',
            'password' => 'salah',
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment(['message' => 'Email atau password salah.']);

        $this->assertGuest();
    }

    // ─── Register JSON API ────────────────────────────────────────────────────

    public function test_vue_register_berhasil_dengan_data_valid(): void
    {
        $response = $this->postJson('/vue/register', [
            'name'                  => 'Vue User Baru',
            'email'                 => 'vuebaru@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'user' => ['id', 'name', 'email', 'role'],
                'message',
            ]);

        $this->assertDatabaseHas('users', ['email' => 'vuebaru@example.com']);
    }

    public function test_vue_register_gagal_jika_email_duplikat(): void
    {
        User::factory()->create(['email' => 'duplikat@example.com']);

        $response = $this->postJson('/vue/register', [
            'name'                  => 'User Baru',
            'email'                 => 'duplikat@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(422);
    }

    // ─── Logout JSON API ──────────────────────────────────────────────────────

    public function test_vue_logout_berhasil(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/vue/logout');

        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'Logout berhasil.']);

        $this->assertGuest();
    }

    // ─── Get User ─────────────────────────────────────────────────────────────

    public function test_vue_user_endpoint_mengembalikan_data_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/vue/user');

        $response->assertStatus(200)
            ->assertJsonPath('user.id', $user->id)
            ->assertJsonPath('user.email', $user->email);
    }

    public function test_vue_user_endpoint_gagal_jika_tidak_login(): void
    {
        $response = $this->getJson('/vue/user');

        // Redirect 302 atau 401
        $this->assertContains($response->status(), [302, 401]);
    }
}
