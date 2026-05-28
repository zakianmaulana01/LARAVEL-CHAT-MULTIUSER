@extends('blade.layouts.app')
@section('title', 'AI Assistant')

@section('content')
<div class="h-full flex flex-col bg-gradient-to-b from-slate-50 to-slate-100">
    {{-- Header --}}
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-4 py-3 flex items-center gap-3 shadow-lg">
        <a href="{{ route('blade.conversations') }}" class="btn-press p-1 rounded-full hover:bg-white/20 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714a2.25 2.25 0 00.659 1.591L19 14.5M14.25 3.104c.251.023.501.05.75.082M19 14.5l-2.47 2.47a2.25 2.25 0 01-1.591.659H9.061a2.25 2.25 0 01-1.591-.659L5 14.5m14 0V17a2 2 0 01-2 2H7a2 2 0 01-2-2v-2.5"/>
            </svg>
        </div>
        <div class="flex-1">
            <h1 class="font-semibold text-lg leading-tight">AI Assistant</h1>
            <p class="text-xs text-white/70">Product FAQ Bot</p>
        </div>
        <button id="btn-clear" class="btn-press p-2 rounded-full hover:bg-white/20 transition" title="Hapus Riwayat">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
        </button>
    </div>

    {{-- Messages --}}
    <div id="chat-messages" class="flex-1 overflow-y-auto p-4 space-y-3 scrollbar-thin">
        @forelse($histories as $chat)
            {{-- User message --}}
            <div class="flex justify-end animate-fade-in">
                <div class="max-w-[80%] bg-gradient-to-br from-emerald-500 to-teal-500 text-white rounded-2xl rounded-tr-sm px-4 py-2 shadow-sm">
                    <p class="text-sm whitespace-pre-wrap">{{ $chat->message }}</p>
                    <p class="text-[10px] text-white/60 mt-1 text-right">{{ $chat->created_at->format('H:i') }}</p>
                </div>
            </div>
            {{-- AI response --}}
            <div class="flex justify-start animate-fade-in">
                <div class="max-w-[80%] bg-gradient-to-br from-indigo-500 to-purple-500 text-white rounded-2xl rounded-tl-sm px-4 py-2 shadow-sm">
                    <p class="text-sm whitespace-pre-wrap">{{ $chat->response }}</p>
                    <p class="text-[10px] text-white/60 mt-1">{{ $chat->created_at->format('H:i') }}</p>
                </div>
            </div>
        @empty
            <div id="empty-state" class="flex flex-col items-center justify-center h-full text-gray-400">
                <svg class="w-16 h-16 mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714a2.25 2.25 0 00.659 1.591L19 14.5M14.25 3.104c.251.023.501.05.75.082M19 14.5l-2.47 2.47a2.25 2.25 0 01-1.591.659H9.061a2.25 2.25 0 01-1.591-.659L5 14.5m14 0V17a2 2 0 01-2 2H7a2 2 0 01-2-2v-2.5"/>
                </svg>
                <p class="text-sm font-medium">Hai! Saya AI Assistant.</p>
                <p class="text-xs mt-1">Tanya apa saja tentang aplikasi ini.</p>
            </div>
        @endforelse

        {{-- Typing indicator --}}
        <div id="typing-indicator" class="flex justify-start hidden">
            <div class="bg-gradient-to-br from-indigo-500 to-purple-500 rounded-2xl rounded-tl-sm px-4 py-3 shadow-sm">
                <div class="flex gap-1">
                    <span class="typing-dot w-2 h-2 bg-white/80 rounded-full"></span>
                    <span class="typing-dot w-2 h-2 bg-white/80 rounded-full"></span>
                    <span class="typing-dot w-2 h-2 bg-white/80 rounded-full"></span>
                </div>
            </div>
        </div>
    </div>

    {{-- Input --}}
    <div class="bg-white border-t px-4 py-3">
        <form id="chat-form" class="flex gap-2">
            @csrf
            <input type="text" id="message-input" name="message" placeholder="Ketik pertanyaan..." maxlength="2000"
                class="flex-1 rounded-full border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent transition"
                autocomplete="off">
            <button type="submit" id="btn-send"
                class="btn-press w-10 h-10 rounded-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white flex items-center justify-center shadow-md hover:shadow-lg transition disabled:opacity-50 disabled:cursor-not-allowed"
                disabled>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                </svg>
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function() {
    const $messages = $('#chat-messages');
    const $input = $('#message-input');
    const $form = $('#chat-form');
    const $btnSend = $('#btn-send');
    const $typing = $('#typing-indicator');
    const $empty = $('#empty-state');

    function scrollBottom() {
        $messages.scrollTop($messages[0].scrollHeight);
    }
    scrollBottom();

    $input.on('input', function() {
        $btnSend.prop('disabled', !$(this).val().trim());
    });

    $form.on('submit', function(e) {
        e.preventDefault();
        const message = $input.val().trim();
        if (!message) return;

        $empty.remove();
        // Append user message
        $messages.find('#typing-indicator').before(`
            <div class="flex justify-end message-enter">
                <div class="max-w-[80%] bg-gradient-to-br from-emerald-500 to-teal-500 text-white rounded-2xl rounded-tr-sm px-4 py-2 shadow-sm">
                    <p class="text-sm whitespace-pre-wrap">${$('<span>').text(message).html()}</p>
                </div>
            </div>
        `);

        $input.val('').prop('disabled', true);
        $btnSend.prop('disabled', true);
        $typing.removeClass('hidden');
        scrollBottom();

        $.ajax({
            url: '{{ route("blade.ai.send") }}',
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: { message },
            success: function(data) {
                $typing.addClass('hidden');
                $messages.find('#typing-indicator').before(`
                    <div class="flex justify-start message-enter">
                        <div class="max-w-[80%] bg-gradient-to-br from-indigo-500 to-purple-500 text-white rounded-2xl rounded-tl-sm px-4 py-2 shadow-sm">
                            <p class="text-sm whitespace-pre-wrap">${$('<span>').text(data.response).html()}</p>
                        </div>
                    </div>
                `);
                scrollBottom();
            },
            error: function() {
                $typing.addClass('hidden');
                $messages.find('#typing-indicator').before(`
                    <div class="flex justify-start message-enter">
                        <div class="max-w-[80%] bg-red-100 text-red-700 rounded-2xl rounded-tl-sm px-4 py-2 shadow-sm">
                            <p class="text-sm">Gagal mengirim pesan. Silakan coba lagi.</p>
                        </div>
                    </div>
                `);
                scrollBottom();
            },
            complete: function() {
                $input.prop('disabled', false).focus();
            }
        });
    });

    $('#btn-clear').on('click', function() {
        if (!confirm('Hapus semua riwayat chat AI?')) return;
        $.ajax({
            url: '{{ route("blade.ai.clear") }}',
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function() {
                location.reload();
            }
        });
    });
});
</script>
@endpush
