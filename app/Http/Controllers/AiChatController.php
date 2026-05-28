<?php

namespace App\Http\Controllers;

use App\Models\AiChatHistory;
use App\Services\GeminiService;
use Illuminate\Http\Request;

class AiChatController extends Controller
{
    public function index(Request $request)
    {
        $histories = $request->user()->aiChatHistories()
            ->orderBy('created_at', 'desc')
            ->take(50)
            ->get()
            ->reverse();

        return view('blade.ai.chat', compact('histories'));
    }

    public function send(Request $request, GeminiService $gemini)
    {
        $validated = $request->validate(['message' => 'required|string|max:2000']);

        $history = $request->user()->aiChatHistories()
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->reverse()
            ->map(fn($h) => [
                ['role' => 'user', 'parts' => [['text' => $h->message]]],
                ['role' => 'model', 'parts' => [['text' => $h->response]]],
            ])
            ->flatten(1)
            ->toArray();

        $response = $gemini->chat($validated['message'], $history);

        $chat = AiChatHistory::create([
            'user_id' => $request->user()->id,
            'message' => $validated['message'],
            'response' => $response,
        ]);

        return response()->json([
            'message' => $chat->message,
            'response' => $chat->response,
            'created_at' => $chat->created_at->toISOString(),
        ]);
    }

    public function clear(Request $request)
    {
        $request->user()->aiChatHistories()->delete();
        return response()->json(['status' => 'ok']);
    }
}
