<?php

namespace Tests\Unit;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Services\ChatService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ChatServiceTest extends TestCase
{
    use RefreshDatabase;

    private ChatService $chatService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->chatService = new ChatService();
    }

    private function buatConversasi(User $user1, User $user2): Conversation
    {
        $conv = Conversation::create(['type' => 'direct', 'created_by' => $user1->id]);
        $conv->participants()->attach([$user1->id, $user2->id]);
        return $conv;
    }

    // ─── getConversationsForUser ───────────────────────────────────────────────

    public function test_getConversationsForUser_mengembalikan_conversation_milik_user(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $user3 = User::factory()->create();

        // user1 punya 1 conversation dengan user2
        $this->buatConversasi($user1, $user2);
        // user3 punya conversation terpisah (bukan milik user1)
        $this->buatConversasi($user2, $user3);

        $conversations = $this->chatService->getConversationsForUser($user1);

        $this->assertCount(1, $conversations);
    }

    public function test_getConversationsForUser_menyertakan_unread_count(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $conv  = $this->buatConversasi($user1, $user2);

        // 3 pesan belum dibaca dari user2
        Message::factory()->count(3)->create([
            'conversation_id' => $conv->id,
            'sender_id'       => $user2->id,
            'is_read'         => false,
        ]);

        $conversations = $this->chatService->getConversationsForUser($user1);

        $this->assertEquals(3, $conversations->first()->unread_count);
    }

    // ─── getMessages ──────────────────────────────────────────────────────────

    public function test_getMessages_mengembalikan_pesan_dengan_paginasi(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $conv  = $this->buatConversasi($user1, $user2);

        Message::factory()->count(5)->create([
            'conversation_id' => $conv->id,
            'sender_id'       => $user1->id,
        ]);

        $result = $this->chatService->getMessages($conv, 50);

        $this->assertCount(5, $result->items());
    }

    public function test_getMessages_tidak_menampilkan_pesan_yang_dihapus_admin(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $conv  = $this->buatConversasi($user1, $user2);

        // 2 pesan normal
        Message::factory()->count(2)->create([
            'conversation_id' => $conv->id,
            'sender_id'       => $user1->id,
            'deleted_by_superadmin' => false,
        ]);

        // 1 pesan dihapus admin
        Message::factory()->create([
            'conversation_id' => $conv->id,
            'sender_id'       => $user1->id,
            'deleted_by_superadmin' => true,
        ]);

        $result = $this->chatService->getMessages($conv, 50);

        $this->assertCount(2, $result->items());
    }

    // ─── sendMessage ──────────────────────────────────────────────────────────

    public function test_sendMessage_menyimpan_pesan_ke_database(): void
    {
        Event::fake();

        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $conv  = $this->buatConversasi($user1, $user2);

        $message = $this->chatService->sendMessage($user1, $conv, 'Halo dari service!');

        $this->assertDatabaseHas('messages', [
            'conversation_id' => $conv->id,
            'sender_id'       => $user1->id,
            'body'            => 'Halo dari service!',
        ]);
    }

    public function test_sendMessage_mengembalikan_instance_message(): void
    {
        Event::fake();

        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $conv  = $this->buatConversasi($user1, $user2);

        $result = $this->chatService->sendMessage($user1, $conv, 'Test pesan');

        $this->assertInstanceOf(Message::class, $result);
        $this->assertEquals('Test pesan', $result->body);
        $this->assertEquals($user1->id, $result->sender_id);
    }

    // ─── markAsRead ───────────────────────────────────────────────────────────

    public function test_markAsRead_mengubah_semua_pesan_orang_lain_menjadi_read(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $conv  = $this->buatConversasi($user1, $user2);

        // Pesan dari user2 belum dibaca oleh user1
        Message::factory()->count(5)->create([
            'conversation_id' => $conv->id,
            'sender_id'       => $user2->id,
            'is_read'         => false,
        ]);

        $this->chatService->markAsRead($conv, $user1);

        $unread = Message::where('conversation_id', $conv->id)
            ->where('sender_id', $user2->id)
            ->where('is_read', false)
            ->count();

        $this->assertEquals(0, $unread);
    }

    public function test_markAsRead_tidak_mengubah_pesan_dari_user_sendiri(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $conv  = $this->buatConversasi($user1, $user2);

        // Pesan dari user1 sendiri (sudah is_read = false karena belum dibaca user2)
        Message::factory()->count(2)->create([
            'conversation_id' => $conv->id,
            'sender_id'       => $user1->id,
            'is_read'         => false,
        ]);

        // user1 mark as read (seharusnya tidak mengubah pesannya sendiri)
        $this->chatService->markAsRead($conv, $user1);

        $count = Message::where('conversation_id', $conv->id)
            ->where('sender_id', $user1->id)
            ->where('is_read', false)
            ->count();

        $this->assertEquals(2, $count);
    }

    // ─── getOrCreateDirectConversation ────────────────────────────────────────

    public function test_getOrCreateDirectConversation_membuat_conversation_baru(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $conv = $this->chatService->getOrCreateDirectConversation($user1, $user2);

        $this->assertInstanceOf(Conversation::class, $conv);
        $this->assertEquals('direct', $conv->type);
        $this->assertDatabaseHas('conversation_user', ['user_id' => $user1->id]);
        $this->assertDatabaseHas('conversation_user', ['user_id' => $user2->id]);
    }

    public function test_getOrCreateDirectConversation_tidak_duplikat(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $conv1 = $this->chatService->getOrCreateDirectConversation($user1, $user2);
        $conv2 = $this->chatService->getOrCreateDirectConversation($user1, $user2);

        $this->assertEquals($conv1->id, $conv2->id);
        $this->assertEquals(1, Conversation::count());
    }

    public function test_getOrCreateDirectConversation_dapat_diakses_dari_kedua_sisi(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // Dibuat dari user1 ke user2
        $conv1 = $this->chatService->getOrCreateDirectConversation($user1, $user2);
        // Dibuat dari user2 ke user1 (harus return yang sama)
        $conv2 = $this->chatService->getOrCreateDirectConversation($user2, $user1);

        $this->assertEquals($conv1->id, $conv2->id);
    }

    // ─── searchUsers ──────────────────────────────────────────────────────────

    public function test_searchUsers_mengembalikan_user_sesuai_nama(): void
    {
        $user   = User::factory()->create();
        $target = User::factory()->create(['name' => 'Budi Santoso Mantap']);
        $other  = User::factory()->create(['name' => 'Orang Lain']);

        $results = $this->chatService->searchUsers($user, 'Budi');

        $this->assertCount(1, $results);
        $this->assertEquals('Budi Santoso Mantap', $results->first()->name);
    }

    public function test_searchUsers_tidak_menyertakan_user_sendiri(): void
    {
        $user = User::factory()->create(['name' => 'Saya Sendiri']);

        $results = $this->chatService->searchUsers($user, 'Saya');

        $this->assertCount(0, $results);
    }

    public function test_searchUsers_tidak_menyertakan_user_yang_di_ban(): void
    {
        $user   = User::factory()->create();
        $banned = User::factory()->create(['name' => 'User Di Ban', 'is_banned' => true]);

        $results = $this->chatService->searchUsers($user, 'Di Ban');

        $this->assertCount(0, $results);
    }
}
