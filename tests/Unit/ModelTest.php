<?php

namespace Tests\Unit;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelTest extends TestCase
{
    use RefreshDatabase;

    // ─── User Model ───────────────────────────────────────────────────────────

    public function test_user_isSuperadmin_mengembalikan_true_untuk_superadmin(): void
    {
        $admin = User::factory()->create(['role' => 'superadmin']);

        $this->assertTrue($admin->isSuperadmin());
    }

    public function test_user_isSuperadmin_mengembalikan_false_untuk_user_biasa(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->assertFalse($user->isSuperadmin());
    }

    public function test_user_isOnline_true_jika_last_seen_kurang_dari_5_menit(): void
    {
        $user = User::factory()->create(['last_seen' => now()->subMinutes(3)]);

        $this->assertTrue($user->isOnline());
    }

    public function test_user_isOnline_false_jika_last_seen_lebih_dari_5_menit(): void
    {
        $user = User::factory()->create(['last_seen' => now()->subMinutes(10)]);

        $this->assertFalse($user->isOnline());
    }

    public function test_user_isOnline_false_jika_last_seen_null(): void
    {
        $user = User::factory()->create(['last_seen' => null]);

        $this->assertFalse($user->isOnline());
    }

    public function test_user_memiliki_relasi_conversations(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $conv = Conversation::create(['type' => 'direct', 'created_by' => $user1->id]);
        $conv->participants()->attach([$user1->id, $user2->id]);

        $this->assertCount(1, $user1->conversations);
        $this->assertInstanceOf(Conversation::class, $user1->conversations->first());
    }

    public function test_user_memiliki_relasi_messages(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $conv = Conversation::create(['type' => 'direct', 'created_by' => $user1->id]);
        $conv->participants()->attach([$user1->id, $user2->id]);

        Message::factory()->count(3)->create([
            'conversation_id' => $conv->id,
            'sender_id'       => $user1->id,
        ]);

        $this->assertCount(3, $user1->messages);
    }

    // ─── Conversation Model ───────────────────────────────────────────────────

    public function test_conversation_memiliki_relasi_participants(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $conv = Conversation::create(['type' => 'direct', 'created_by' => $user1->id]);
        $conv->participants()->attach([$user1->id, $user2->id]);

        $this->assertCount(2, $conv->participants);
    }

    public function test_conversation_latestMessage_mengembalikan_pesan_terakhir(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $conv = Conversation::create(['type' => 'direct', 'created_by' => $user1->id]);
        $conv->participants()->attach([$user1->id, $user2->id]);

        $pesan1 = Message::factory()->create([
            'conversation_id' => $conv->id,
            'sender_id'       => $user1->id,
            'body'            => 'Pesan pertama',
            'created_at'      => now()->subMinutes(5),
        ]);

        $pesan2 = Message::factory()->create([
            'conversation_id' => $conv->id,
            'sender_id'       => $user2->id,
            'body'            => 'Pesan terakhir',
            'created_at'      => now(),
        ]);

        $this->assertEquals('Pesan terakhir', $conv->latestMessage->body);
    }

    public function test_conversation_unreadMessagesFor_menghitung_pesan_belum_dibaca(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $conv = Conversation::create(['type' => 'direct', 'created_by' => $user1->id]);
        $conv->participants()->attach([$user1->id, $user2->id]);

        // 3 pesan belum dibaca dari user2 ke user1
        Message::factory()->count(3)->create([
            'conversation_id' => $conv->id,
            'sender_id'       => $user2->id,
            'is_read'         => false,
        ]);

        // 1 pesan sudah dibaca
        Message::factory()->create([
            'conversation_id' => $conv->id,
            'sender_id'       => $user2->id,
            'is_read'         => true,
        ]);

        $this->assertEquals(3, $conv->unreadMessagesFor($user1));
    }

    public function test_conversation_getOtherParticipant_mengembalikan_user_lain(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $conv = Conversation::create(['type' => 'direct', 'created_by' => $user1->id]);
        $conv->participants()->attach([$user1->id, $user2->id]);
        $conv->load('participants');

        $other = $conv->getOtherParticipant($user1);

        $this->assertEquals($user2->id, $other->id);
    }

    // ─── Message Model ────────────────────────────────────────────────────────

    public function test_message_memiliki_relasi_sender(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $conv = Conversation::create(['type' => 'direct', 'created_by' => $user1->id]);
        $conv->participants()->attach([$user1->id, $user2->id]);

        $message = Message::factory()->create([
            'conversation_id' => $conv->id,
            'sender_id'       => $user1->id,
        ]);

        $this->assertEquals($user1->id, $message->sender->id);
    }

    public function test_message_memiliki_relasi_conversation(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $conv = Conversation::create(['type' => 'direct', 'created_by' => $user1->id]);
        $conv->participants()->attach([$user1->id, $user2->id]);

        $message = Message::factory()->create([
            'conversation_id' => $conv->id,
            'sender_id'       => $user1->id,
        ]);

        $this->assertEquals($conv->id, $message->conversation->id);
    }

    public function test_message_deleted_by_superadmin_default_false(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $conv = Conversation::create(['type' => 'direct', 'created_by' => $user1->id]);
        $conv->participants()->attach([$user1->id, $user2->id]);

        $message = Message::factory()->create([
            'conversation_id' => $conv->id,
            'sender_id'       => $user1->id,
        ]);

        $this->assertFalse($message->deleted_by_superadmin);
    }
}
