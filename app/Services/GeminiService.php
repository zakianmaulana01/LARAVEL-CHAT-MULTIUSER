<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    private string $apiKey;
    private string $model = 'gemini-2.0-flash';
    private string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key', env('GEMINI_API_KEY', ''));
    }

    public function chat(string $message, array $history = []): string
    {
        $systemInstruction = 'Kamu adalah AI assistant untuk aplikasi Chat Multiuser. Jawab pertanyaan seputar fitur chat realtime, cara registrasi, cara mengirim pesan, fitur admin panel, dan troubleshooting. Jika pengguna bertanya di luar topik aplikasi, arahkan kembali ke topik produk dengan sopan. Jawab dalam Bahasa Indonesia.';

        $contents = $history;
        $contents[] = ['role' => 'user', 'parts' => [['text' => $message]]];

        $payload = [
            'system_instruction' => [
                'parts' => [['text' => $systemInstruction]],
            ],
            'contents' => $contents,
            'generationConfig' => [
                'temperature' => 0.7,
                'maxOutputTokens' => 1024,
            ],
        ];

        try {
            $response = Http::timeout(30)
                ->post("{$this->baseUrl}/models/{$this->model}:generateContent?key={$this->apiKey}", $payload);

            if ($response->successful()) {
                $data = $response->json();
                return $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Maaf, saya tidak bisa memproses permintaan tersebut.';
            }

            Log::error('Gemini API error', ['status' => $response->status(), 'body' => $response->body()]);
            return 'Maaf, terjadi kesalahan saat menghubungi AI. Silakan coba lagi nanti.';
        } catch (\Exception $e) {
            Log::error('Gemini API exception', ['message' => $e->getMessage()]);
            return 'Maaf, terjadi kesalahan koneksi. Silakan coba lagi nanti.';
        }
    }
}
