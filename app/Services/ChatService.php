<?php

namespace App\Services;

use App\Events\MessageSent;
use App\Events\NotificationSent;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;

class ChatService
{
    public function getConversationsForUser(User $user)
    {
        return $user->conversations()
            ->with(['participants', 'latestMessage.sender'])
            ->withCount(['messages as unread_count' => function ($query) use ($user) {
                $query->where('sender_id', '!=', $user->id)
                    ->where('is_read', false);
            }])
            ->orderByDesc(
                Message::select('created_at')
                    ->whereColumn('conversation_id', 'conversations.id')
                    ->latest()
                    ->limit(1)
            )
            ->get();
    }

    public function getMessages(Conversation $conversation, int $perPage = 50)
    {
        return $conversation->messages()
            ->with('sender')
            ->where('deleted_by_superadmin', false)
            ->orderBy('created_at', 'asc')
            ->paginate($perPage);
    }

    public function sendMessage(User $sender, Conversation $conversation, string $body, ?string $filePath = null): Message
    {
        $message = $conversation->messages()->create([
            'sender_id' => $sender->id,
            'body' => $body,
            'file_path' => $filePath,
        ]);

        $message->load('sender');

        // Broadcast ke conversation channel
        broadcast(new MessageSent($message))->toOthers();

        // Kirim notifikasi ke semua participant kecuali sender
        $conversation->participants()
            ->where('users.id', '!=', $sender->id)
            ->get()
            ->each(function ($participant) use ($message) {
                broadcast(new NotificationSent($participant, $message));
            });

        return $message;
    }

    public function markAsRead(Conversation $conversation, User $user): void
    {
        $conversation->messages()
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    public function getOrCreateDirectConversation(User $user1, User $user2): Conversation
    {
        // Cari conversation direct yang sudah ada antara 2 user
        $conversation = Conversation::where('type', 'direct')
            ->whereHas('participants', fn($q) => $q->where('users.id', $user1->id))
            ->whereHas('participants', fn($q) => $q->where('users.id', $user2->id))
            ->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'type' => 'direct',
                'created_by' => $user1->id,
            ]);
            $conversation->participants()->attach([$user1->id, $user2->id]);
        }

        return $conversation->load('participants');
    }

    public function searchUsers(User $currentUser, string $query)
    {
        return User::where('id', '!=', $currentUser->id)
            ->where('is_banned', false)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%");
            })
            ->limit(20)
            ->get(['id', 'name', 'email', 'avatar', 'last_seen']);
    }
}
