<?php

namespace Tests\Feature;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ChatTest extends TestCase
{
    use RefreshDatabase;

    private function buatConversationDenganPeserta(User $user1, User $user2): Conversation
    {
        $conversation = Conversation::create([
            'type'       => 'direct',
            'created_by' => $user1->id,
        ]);
        $conversation->participants()->attach([$user1->id, $user2->id]);

        return $conversation;
    }

    // ─── Halaman Conversations ────────────────────────────────────────────────

    public function test_user_bisa_akses_halaman_conversations(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/blade/conversations');

        $response->assertStatus(200);
    }

    public function test_vue_conversations_mengembalikan_json(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/vue/conversations');

        $response->assertStatus(200)
            ->assertJsonStructure(['conversations']);
    }

    // ─── Mulai Conversation ───────────────────────────────────────────────────

    public function test_bisa_mulai_conversation_baru_dengan_user_lain(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $response = $this->actingAs($user1)->postJson('/vue/conversations/start', [
            'user_id' => $user2->id,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['conversation' => ['id', 'type']]);

        $this->assertDatabaseHas('conversations', ['type' => 'direct']);
        $this->assertDatabaseHas('conversation_user', ['user_id' => $user1->id]);
        $this->assertDatabaseHas('conversation_user', ['user_id' => $user2->id]);
    }

    public function test_mulai_conversation_dengan_user_yang_sama_tidak_duplikat(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // Pertama kali
        $this->actingAs($user1)->postJson('/vue/conversations/start', ['user_id' => $user2->id]);
        // Kedua kali
        $this->actingAs($user1)->postJson('/vue/conversations/start', ['user_id' => $user2->id]);

        // Hanya boleh ada satu conversation direct
        $this->assertEquals(1, Conversation::where('type', 'direct')->count());
    }

    public function test_tidak_bisa_mulai_conversation_dengan_user_yang_di_ban(): void
    {
        $user1  = User::factory()->create();
        $banned = User::factory()->create(['is_banned' => true]);

        $response = $this->actingAs($user1)->postJson('/vue/conversations/start', [
            'user_id' => $banned->id,
        ]);

        // User yang dibanned tidak muncul di search & tidak bisa di-start
        $response->assertStatus(422);
    }

    // ─── Lihat Pesan ─────────────────────────────────────────────────────────

    public function test_user_bisa_lihat_pesan_dalam_conversation_miliknya(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $conv  = $this->buatConversationDenganPeserta($user1, $user2);

        Message::factory()->create([
            'conversation_id' => $conv->id,
            'sender_id'       => $user2->id,
            'body'            => 'Halo dari user2!',
        ]);

        $response = $this->actingAs($user1)->getJson("/vue/conversations/{$conv->id}");

        $response->assertStatus(200)
            ->assertJsonStructure(['conversation', 'messages'])
            ->assertJsonPath('messages.data.0.body', 'Halo dari user2!');
    }

    public function test_user_tidak_bisa_lihat_conversation_orang_lain(): void
    {
        $user1    = User::factory()->create();
        $user2    = User::factory()->create();
        $outsider = User::factory()->create();

        $conv = $this->buatConversationDenganPeserta($user1, $user2);

        $response = $this->actingAs($outsider)->getJson("/vue/conversations/{$conv->id}");

        $response->assertStatus(403);
    }

    // ─── Kirim Pesan ─────────────────────────────────────────────────────────

    public function test_user_bisa_kirim_pesan_ke_conversation_miliknya(): void
    {
        Event::fake();

        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $conv  = $this->buatConversationDenganPeserta($user1, $user2);

        $response = $this->actingAs($user1)->postJson('/vue/messages', [
            'conversation_id' => $conv->id,
            'body'            => 'Halo, ini pesan pertama!',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['message' => ['id', 'body', 'sender']]);

        $this->assertDatabaseHas('messages', [
            'conversation_id' => $conv->id,
            'sender_id'       => $user1->id,
            'body'            => 'Halo, ini pesan pertama!',
        ]);
    }

    public function test_kirim_pesan_gagal_jika_body_kosong(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $conv  = $this->buatConversationDenganPeserta($user1, $user2);

        $response = $this->actingAs($user1)->postJson('/vue/messages', [
            'conversation_id' => $conv->id,
            'body'            => '',
        ]);

        $response->assertStatus(422);
    }

    public function test_user_tidak_bisa_kirim_pesan_ke_conversation_orang_lain(): void
    {
        Event::fake();

        $user1    = User::factory()->create();
        $user2    = User::factory()->create();
        $outsider = User::factory()->create();
        $conv     = $this->buatConversationDenganPeserta($user1, $user2);

        $response = $this->actingAs($outsider)->postJson('/vue/messages', [
            'conversation_id' => $conv->id,
            'body'            => 'Saya outsider!',
        ]);

        $response->assertStatus(403);
    }

    // ─── Mark as Read ─────────────────────────────────────────────────────────

    public function test_mark_as_read_mengupdate_status_pesan(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $conv  = $this->buatConversationDenganPeserta($user1, $user2);

        // 3 pesan belum dibaca dari user2
        Message::factory()->count(3)->create([
            'conversation_id' => $conv->id,
            'sender_id'       => $user2->id,
            'is_read'         => false,
        ]);

        $this->actingAs($user1)->postJson("/vue/conversations/{$conv->id}/read");

        // Semua pesan dari user2 ke user1 harus sudah dibaca
        $this->assertEquals(0, Message::where('conversation_id', $conv->id)
            ->where('sender_id', $user2->id)
            ->where('is_read', false)
            ->count()
        );
    }

    // ─── Search Users ─────────────────────────────────────────────────────────

    public function test_search_users_mengembalikan_hasil_sesuai_query(): void
    {
        $user    = User::factory()->create();
        $target  = User::factory()->create(['name' => 'Budi Santoso']);
        $other   = User::factory()->create(['name' => 'Dewi Lestari']);

        $response = $this->actingAs($user)->getJson('/vue/users/search?q=Budi');

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Budi Santoso'])
            ->assertJsonMissing(['name' => 'Dewi Lestari']);
    }

    public function test_search_users_tidak_menampilkan_user_yang_di_ban(): void
    {
        $user   = User::factory()->create();
        $banned = User::factory()->create(['name' => 'User Banned', 'is_banned' => true]);

        $response = $this->actingAs($user)->getJson('/vue/users/search?q=Banned');

        $response->assertStatus(200)
            ->assertJsonMissing(['name' => 'User Banned']);
    }

    public function test_search_users_tidak_menampilkan_diri_sendiri(): void
    {
        $user = User::factory()->create(['name' => 'Saya Sendiri']);

        $response = $this->actingAs($user)->getJson('/vue/users/search?q=Saya');

        $response->assertStatus(200)
            ->assertJsonMissing(['name' => 'Saya Sendiri']);
    }

    public function test_search_gagal_jika_query_kurang_dari_2_karakter(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/vue/users/search?q=A');

        $response->assertStatus(422);
    }

    // ─── Typing Indicator ─────────────────────────────────────────────────────

    public function test_typing_endpoint_berhasil_dipanggil(): void
    {
        Event::fake();

        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $conv  = $this->buatConversationDenganPeserta($user1, $user2);

        $response = $this->actingAs($user1)->postJson('/vue/typing', [
            'conversation_id' => $conv->id,
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['status' => 'ok']);
    }
}
