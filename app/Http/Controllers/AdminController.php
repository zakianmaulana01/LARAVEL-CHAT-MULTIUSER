<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        $stats = [
            'total_users' => User::where('role', 'user')->count(),
            'total_messages' => Message::count(),
            'messages_today' => Message::whereDate('created_at', today())->count(),
            'active_conversations' => Conversation::whereHas('messages', function ($q) {
                $q->where('created_at', '>=', now()->subDay());
            })->count(),
            'banned_users' => User::where('is_banned', true)->count(),
        ];

        if ($request->expectsJson()) {
            return response()->json(['stats' => $stats]);
        }

        return view('blade.admin.dashboard', compact('stats'));
    }

    public function users(Request $request)
    {
        $users = User::withCount('messages')
            ->orderByDesc('created_at')
            ->paginate(20);

        if ($request->expectsJson()) {
            return response()->json(['users' => $users]);
        }

        return view('blade.admin.users', compact('users'));
    }

    public function banUser(Request $request, User $user)
    {
        if ($user->isSuperadmin()) {
            $message = 'Tidak bisa mem-ban superadmin.';
            return $request->expectsJson()
                ? response()->json(['message' => $message], 403)
                : back()->with('error', $message);
        }

        $user->update(['is_banned' => !$user->is_banned]);
        $status = $user->is_banned ? 'diblokir' : 'diaktifkan kembali';

        if ($request->expectsJson()) {
            return response()->json(['message' => "User berhasil {$status}.", 'user' => $user]);
        }

        return back()->with('success', "User {$user->name} berhasil {$status}.");
    }

    public function destroyUser(Request $request, User $user)
    {
        if ($user->isSuperadmin()) {
            $message = 'Tidak bisa menghapus superadmin.';
            return $request->expectsJson()
                ? response()->json(['message' => $message], 403)
                : back()->with('error', $message);
        }

        $userName = $user->name;
        $user->delete();

        if ($request->expectsJson()) {
            return response()->json(['message' => "User {$userName} berhasil dihapus."]);
        }

        return back()->with('success', "User {$userName} berhasil dihapus.");
    }

    public function destroyMessage(Request $request, Message $message)
    {
        $message->update(['deleted_by_superadmin' => true]);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Pesan berhasil dihapus.']);
        }

        return back()->with('success', 'Pesan berhasil dihapus oleh admin.');
    }

    public function messages(Request $request)
    {
        $messages = Message::with(['sender', 'conversation'])
            ->orderByDesc('created_at')
            ->paginate(50);

        if ($request->expectsJson()) {
            return response()->json(['messages' => $messages]);
        }

        return view('blade.admin.messages', compact('messages'));
    }

    public function monitor(Request $request)
    {
        $search = $request->input('search');

        $conversations = Conversation::with(['participants', 'latestMessage.sender'])
            ->withCount('messages')
            ->when($search, function ($q) use ($search) {
                $q->whereHas('participants', function ($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('updated_at')
            ->paginate(20);

        return view('blade.admin.monitor', compact('conversations', 'search'));
    }

    public function monitorShow(Request $request, Conversation $conversation)
    {
        $messages = $conversation->messages()
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->paginate(100);

        $conversation->load('participants');

        return view('blade.admin.monitor-show', compact('conversation', 'messages'));
    }
}
