<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BladeAuthTest extends TestCase
{
    use RefreshDatabase;

    // ─── Halaman Login ────────────────────────────────────────────────────────

    public function test_halaman_login_dapat_diakses(): void
    {
        $response = $this->get('/blade/login');

        $response->assertStatus(200);
        $response->assertSee('Masuk');
    }

    public function test_halaman_register_dapat_diakses(): void
    {
        $response = $this->get('/blade/register');

        $response->assertStatus(200);
        $response->assertSee('Daftar');
    }

    public function test_user_bisa_login_dengan_kredensial_valid(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/blade/login', [
            'email'    => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/blade');
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_gagal_dengan_password_salah(): void
    {
        User::factory()->create(['email' => 'test@example.com']);

        $response = $this->post('/blade/login', [
            'email'    => 'test@example.com',
            'password' => 'password-salah',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_login_gagal_jika_user_tidak_ada(): void
    {
        $response = $this->post('/blade/login', [
            'email'    => 'tidakada@example.com',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
    }

    // ─── Register ─────────────────────────────────────────────────────────────

    public function test_user_bisa_register_dengan_data_valid(): void
    {
        $response = $this->post('/blade/register', [
            'name'                  => 'User Baru',
            'email'                 => 'baru@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/blade');
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'baru@example.com',
            'name'  => 'User Baru',
            'role'  => 'user',
        ]);
    }

    public function test_register_gagal_jika_email_sudah_dipakai(): void
    {
        User::factory()->create(['email' => 'sudahada@example.com']);

        $response = $this->post('/blade/register', [
            'name'                  => 'Orang Lain',
            'email'                 => 'sudahada@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_register_gagal_jika_password_tidak_cocok(): void
    {
        $response = $this->post('/blade/register', [
            'name'                  => 'User Test',
            'email'                 => 'test@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'passwordbeda',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_register_gagal_jika_nama_kosong(): void
    {
        $response = $this->post('/blade/register', [
            'name'                  => '',
            'email'                 => 'test@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('name');
    }

    // ─── Logout ───────────────────────────────────────────────────────────────

    public function test_user_bisa_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/blade/logout');

        $response->assertRedirect('/blade/login');
        $this->assertGuest();
    }

    // ─── Middleware Auth ──────────────────────────────────────────────────────

    public function test_halaman_chat_redirect_ke_login_jika_belum_login(): void
    {
        $response = $this->get('/blade/conversations');

        $response->assertRedirect('/blade/login');
    }

    public function test_user_yang_di_ban_tidak_bisa_akses_chat(): void
    {
        $banned = User::factory()->create(['is_banned' => true]);

        $response = $this->actingAs($banned)->get('/blade/conversations');

        // Harus di-logout dan redirect ke login
        $response->assertRedirect('/blade/login');
        $this->assertGuest();
    }
}
