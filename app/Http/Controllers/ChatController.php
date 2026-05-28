<?php

namespace App\Http\Controllers;

use App\Events\TypingStarted;
use App\Models\Conversation;
use App\Models\User;
use App\Services\ChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{
    public function __construct(private ChatService $chatService) {}

    public function index(Request $request)
    {
        $user = $request->user();
        $user->update(['last_seen' => now()]);

        $conversations = $this->chatService->getConversationsForUser($user);

        if ($request->expectsJson()) {
            return response()->json(['conversations' => $conversations]);
        }

        return view('blade.chat.index', compact('conversations'));
    }

    public function show(Request $request, Conversation $conversation)
    {
        // Pastikan user adalah participant
        if (!$conversation->participants()->where('users.id', $request->user()->id)->exists()) {
            abort(403);
        }

        $messages = $this->chatService->getMessages($conversation);
        $this->chatService->markAsRead($conversation, $request->user());

        if ($request->expectsJson()) {
            return response()->json([
                'conversation' => $conversation->load('participants'),
                'messages' => $messages,
            ]);
        }

        $conversations = $this->chatService->getConversationsForUser($request->user());
        return view('blade.chat.index', compact('conversations', 'conversation', 'messages'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'conversation_id' => ['required', 'exists:conversations,id'],
            'body' => ['required_without:file', 'nullable', 'string', 'max:5000'],
            'file' => ['nullable', 'file', 'max:10240'], // 10MB max
        ]);

        $conversation = Conversation::findOrFail($validated['conversation_id']);

        // Pastikan user adalah participant
        if (!$conversation->participants()->where('users.id', $request->user()->id)->exists()) {
            abort(403);
        }

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('chat-files', 'public');
        }

        $message = $this->chatService->sendMessage(
            $request->user(),
            $conversation,
            $validated['body'] ?? '',
            $filePath
        );

        if ($request->expectsJson()) {
            return response()->json(['message' => $message->load('sender')], 201);
        }

        return back();
    }

    public function markRead(Request $request, Conversation $conversation)
    {
        if (!$conversation->participants()->where('users.id', $request->user()->id)->exists()) {
            abort(403);
        }

        $this->chatService->markAsRead($conversation, $request->user());

        if ($request->expectsJson()) {
            return response()->json(['status' => 'ok']);
        }

        return back();
    }

    public function startConversation(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        $otherUser = User::findOrFail($validated['user_id']);
        $conversation = $this->chatService->getOrCreateDirectConversation($request->user(), $otherUser);

        if ($request->expectsJson()) {
            return response()->json(['conversation' => $conversation], 201);
        }

        return redirect()->route('blade.conversations.show', $conversation);
    }

    public function typing(Request $request)
    {
        $validated = $request->validate([
            'conversation_id' => ['required', 'exists:conversations,id'],
        ]);

        broadcast(new TypingStarted(
            (int) $validated['conversation_id'],
            $request->user()
        ))->toOthers();

        return response()->json(['status' => 'ok']);
    }

    public function searchUsers(Request $request)
    {
        $validated = $request->validate([
            'q' => ['required', 'string', 'min:2'],
        ]);

        $users = $this->chatService->searchUsers($request->user(), $validated['q']);

        return response()->json(['users' => $users]);
    }
}
