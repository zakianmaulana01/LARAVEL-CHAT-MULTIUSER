<?php

namespace Tests\Feature;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    private function buatSuperadmin(): User
    {
        return User::factory()->create(['role' => 'superadmin']);
    }

    private function buatUserBiasa(): User
    {
        return User::factory()->create(['role' => 'user']);
    }

    private function buatConversasiDenganPesan(User $user1, User $user2, int $jumlahPesan = 3): Conversation
    {
        $conv = Conversation::create(['type' => 'direct', 'created_by' => $user1->id]);
        $conv->participants()->attach([$user1->id, $user2->id]);

        Message::factory()->count($jumlahPesan)->create([
            'conversation_id' => $conv->id,
            'sender_id'       => $user1->id,
        ]);

        return $conv;
    }

    // ─── Akses Admin Panel ────────────────────────────────────────────────────

    public function test_superadmin_bisa_akses_dashboard(): void
    {
        $admin = $this->buatSuperadmin();

        $response = $this->actingAs($admin)->get('/blade/admin');

        $response->assertStatus(200);
    }

    public function test_user_biasa_tidak_bisa_akses_admin_dashboard(): void
    {
        $user = $this->buatUserBiasa();

        $response = $this->actingAs($user)->get('/blade/admin');

        $response->assertStatus(403);
    }

    public function test_guest_tidak_bisa_akses_admin(): void
    {
        $response = $this->get('/blade/admin');

        $response->assertRedirect('/blade/login');
    }

    // ─── Dashboard Stats ──────────────────────────────────────────────────────

    public function test_dashboard_api_mengembalikan_stats_benar(): void
    {
        $admin = $this->buatSuperadmin();
        $user1 = $this->buatUserBiasa();
        $user2 = $this->buatUserBiasa();

        $this->buatConversasiDenganPesan($user1, $user2, 5);

        $response = $this->actingAs($admin)->getJson('/vue/admin/dashboard');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'stats' => [
                    'total_users',
                    'total_messages',
                    'messages_today',
                    'active_conversations',
                    'banned_users',
                ],
            ]);

        // 2 user biasa yang kita buat
        $this->assertEquals(2, $response->json('stats.total_users'));
    }

    // ─── Kelola User ──────────────────────────────────────────────────────────

    public function test_superadmin_bisa_lihat_daftar_user(): void
    {
        $admin = $this->buatSuperadmin();
        User::factory()->count(5)->create();

        $response = $this->actingAs($admin)->getJson('/vue/admin/users');

        $response->assertStatus(200)
            ->assertJsonStructure(['users']);
    }

    public function test_superadmin_bisa_ban_user(): void
    {
        $admin = $this->buatSuperadmin();
        $user  = $this->buatUserBiasa();

        $response = $this->actingAs($admin)->postJson("/vue/admin/users/{$user->id}/ban");

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', ['id' => $user->id, 'is_banned' => true]);
    }

    public function test_superadmin_bisa_unban_user_yang_sudah_di_ban(): void
    {
        $admin  = $this->buatSuperadmin();
        $banned = User::factory()->create(['is_banned' => true]);

        $response = $this->actingAs($admin)->postJson("/vue/admin/users/{$banned->id}/ban");

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', ['id' => $banned->id, 'is_banned' => false]);
    }

    public function test_superadmin_tidak_bisa_ban_superadmin_lain(): void
    {
        $admin1 = $this->buatSuperadmin();
        $admin2 = $this->buatSuperadmin();

        $response = $this->actingAs($admin1)->postJson("/vue/admin/users/{$admin2->id}/ban");

        $response->assertStatus(403);
    }

    public function test_superadmin_bisa_hapus_user(): void
    {
        $admin = $this->buatSuperadmin();
        $user  = $this->buatUserBiasa();

        $response = $this->actingAs($admin)->deleteJson("/vue/admin/users/{$user->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_superadmin_tidak_bisa_hapus_dirinya_sendiri(): void
    {
        $admin = $this->buatSuperadmin();

        $response = $this->actingAs($admin)->deleteJson("/vue/admin/users/{$admin->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    // ─── Moderasi Pesan ───────────────────────────────────────────────────────

    public function test_superadmin_bisa_lihat_semua_pesan(): void
    {
        $admin = $this->buatSuperadmin();
        $user1 = $this->buatUserBiasa();
        $user2 = $this->buatUserBiasa();
        $this->buatConversasiDenganPesan($user1, $user2, 3);

        $response = $this->actingAs($admin)->getJson('/vue/admin/messages');

        $response->assertStatus(200)
            ->assertJsonStructure(['messages']);
    }

    public function test_superadmin_bisa_hapus_pesan_user(): void
    {
        $admin   = $this->buatSuperadmin();
        $user1   = $this->buatUserBiasa();
        $user2   = $this->buatUserBiasa();
        $conv    = $this->buatConversasiDenganPesan($user1, $user2, 1);
        $message = Message::first();

        $response = $this->actingAs($admin)->deleteJson("/vue/admin/messages/{$message->id}");

        $response->assertStatus(200);
        $this->assertDatabaseHas('messages', [
            'id'                   => $message->id,
            'deleted_by_superadmin' => true,
        ]);
    }

    public function test_pesan_yang_dihapus_admin_tidak_tampil_di_chat_biasa(): void
    {
        $admin = $this->buatSuperadmin();
        $user1 = $this->buatUserBiasa();
        $user2 = $this->buatUserBiasa();
        $conv  = $this->buatConversasiDenganPesan($user1, $user2, 2);

        // Hapus pesan pertama
        $pesan = $conv->messages()->first();
        $pesan->update(['deleted_by_superadmin' => true]);

        // User biasa lihat conversation
        $response = $this->actingAs($user1)->getJson("/vue/conversations/{$conv->id}");

        $response->assertStatus(200);
        $data = $response->json('messages.data');
        // Pesan yang dihapus tidak muncul
        foreach ($data as $msg) {
            $this->assertNotEquals($pesan->id, $msg['id']);
        }
    }

    // ─── User Biasa Tidak Bisa Akses Admin API ────────────────────────────────

    public function test_user_biasa_tidak_bisa_akses_admin_api(): void
    {
        $user = $this->buatUserBiasa();

        $endpoints = [
            ['GET', '/vue/admin/dashboard'],
            ['GET', '/vue/admin/users'],
            ['GET', '/vue/admin/messages'],
        ];

        foreach ($endpoints as [$method, $url]) {
            $response = $this->actingAs($user)->json($method, $url);
            $this->assertEquals(403, $response->status(), "Endpoint {$url} seharusnya 403 untuk user biasa");
        }
    }
}
