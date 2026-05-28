@extends('blade.layouts.app')
@section('title', 'Chat')

@section('content')
<div x-data="chatApp()" x-init="init()" class="h-screen flex overflow-hidden bg-gray-100">
    {{-- Sidebar --}}
    <div class="w-full md:w-96 lg:w-[420px] flex flex-col border-r border-gray-200 bg-white"
         :class="{ 'hidden md:flex': activeConversation }">

        {{-- Sidebar Header --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 bg-white">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-emerald-400 to-teal-600 flex items-center justify-center text-white font-bold text-sm shadow-sm">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div>
                    <h1 class="text-lg font-bold text-gray-900">Chat</h1>
                    <p class="text-xs text-gray-500">{{ auth()->user()->name }}</p>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                {{-- Notification Bell --}}
                <div class="relative" x-data="{ showNotif: false }">
                    <button @click="showNotif = !showNotif" class="btn-press p-2 rounded-full hover:bg-gray-100 transition-colors relative">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span x-show="unreadTotal > 0" x-text="unreadTotal" class="badge-pop absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center font-bold"></span>
                    </button>
                </div>

                @if(auth()->user()->isSuperadmin())
                <a href="{{ route('blade.admin.dashboard') }}" class="btn-press p-2 rounded-full hover:bg-gray-100 transition-colors" title="Admin Panel">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </a>
                @endif

                <form method="POST" action="{{ route('blade.logout') }}">
                    @csrf
                    <button type="submit" class="btn-press p-2 rounded-full hover:bg-gray-100 transition-colors" title="Keluar">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>

        {{-- Search --}}
        <div class="px-4 py-3">
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" x-model="searchQuery" @input.debounce.300ms="searchUsers()"
                    placeholder="Cari atau mulai chat baru..."
                    class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border-0 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all duration-200">
            </div>

            {{-- Search Results --}}
            <div x-show="searchResults.length > 0" x-cloak class="mt-2 bg-white border border-gray-100 rounded-xl shadow-lg overflow-hidden">
                <template x-for="user in searchResults" :key="user.id">
                    <button @click="startChat(user.id)" class="btn-press w-full flex items-center px-4 py-3 hover:bg-gray-50 transition-colors">
                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center text-white text-xs font-bold">
                            <span x-text="user.name.charAt(0).toUpperCase()"></span>
                        </div>
                        <div class="ml-3 text-left">
                            <p class="text-sm font-medium text-gray-900" x-text="user.name"></p>
                            <p class="text-xs text-gray-500" x-text="user.email"></p>
                        </div>
                    </button>
                </template>
            </div>
        </div>

        {{-- Conversation List --}}
        <div class="flex-1 overflow-y-auto scrollbar-thin">
            @forelse($conversations ?? [] as $index => $conv)
            <button wire:key="conv-{{ $conv->id }}"
                @click="openConversation({{ $conv->id }})"
                class="btn-press w-full flex items-center px-5 py-3.5 hover:bg-gray-50 transition-all duration-150 border-b border-gray-50 animate-fade-in"
                style="animation-delay: {{ $index * 50 }}ms"
                :class="{ 'bg-emerald-50 border-l-4 border-l-emerald-500': activeConversation == {{ $conv->id }} }">

                {{-- Avatar --}}
                <div class="relative flex-shrink-0">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center text-white font-bold shadow-sm">
                        {{ strtoupper(substr($conv->getOtherParticipant(auth()->user())->name ?? 'U', 0, 1)) }}
                    </div>
                    @if($conv->getOtherParticipant(auth()->user())?->isOnline())
                    <span class="absolute bottom-0 right-0 w-3.5 h-3.5 bg-green-400 border-2 border-white rounded-full"></span>
                    @endif
                </div>

                {{-- Content --}}
                <div class="ml-3.5 flex-1 min-w-0 text-left">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-semibold text-gray-900 truncate">
                            {{ $conv->getOtherParticipant(auth()->user())->name ?? 'User' }}
                        </p>
                        @if($conv->latestMessage)
                        <span class="text-xs text-gray-400 flex-shrink-0 ml-2">
                            {{ $conv->latestMessage->created_at->diffForHumans(null, true) }}
                        </span>
                        @endif
                    </div>
                    <div class="flex items-center justify-between mt-0.5">
                        <p class="text-sm text-gray-500 truncate">
                            @if($conv->latestMessage)
                                @if($conv->latestMessage->sender_id == auth()->id())
                                    <span class="text-emerald-600">Anda:</span>
                                @endif
                                {{ Str::limit($conv->latestMessage->body, 35) }}
                            @else
                                <span class="italic">Belum ada pesan</span>
                            @endif
                        </p>
                        @if($conv->unread_count > 0)
                        <span class="badge-pop ml-2 flex-shrink-0 w-5 h-5 bg-emerald-500 text-white text-xs rounded-full flex items-center justify-center font-bold">
                            {{ $conv->unread_count > 9 ? '9+' : $conv->unread_count }}
                        </span>
                        @endif
                    </div>
                </div>
            </button>
            @empty
            <div class="flex flex-col items-center justify-center h-64 text-gray-400">
                <svg class="w-16 h-16 mb-4 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <p class="text-sm">Belum ada percakapan</p>
                <p class="text-xs mt-1">Cari seseorang untuk mulai chat</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Chat Window --}}
    <div class="flex-1 flex flex-col bg-gray-50"
         :class="{ 'hidden md:flex': !activeConversation }">

        {{-- Chat Header --}}
        <div x-show="activeConversation" class="flex items-center px-5 py-3.5 bg-white border-b border-gray-100 shadow-sm">
            <button @click="closeConversation()" class="md:hidden btn-press mr-3 p-1 rounded-full hover:bg-gray-100">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center text-white font-bold text-sm shadow-sm">
                <span x-text="activeName ? activeName.charAt(0).toUpperCase() : ''"></span>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-semibold text-gray-900" x-text="activeName"></h3>
                <p class="text-xs" :class="isOnline ? 'text-green-500' : 'text-gray-400'">
                    <span x-show="typingUser" x-text="typingUser + ' sedang mengetik...'" class="text-emerald-500 italic"></span>
                    <span x-show="!typingUser" x-text="isOnline ? 'Online' : 'Offline'"></span>
                </p>
            </div>
        </div>

        {{-- Messages Area --}}
        <div x-ref="messagesContainer" class="flex-1 overflow-y-auto px-4 py-4 space-y-2 scrollbar-thin" style="background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 50%, #f0f9ff 100%);">
            {{-- Empty State --}}
            <div x-show="!activeConversation" class="flex flex-col items-center justify-center h-full text-gray-400">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-12 h-12 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                </div>
                <p class="text-base font-medium text-gray-500">Pilih percakapan</p>
                <p class="text-sm mt-1">atau cari seseorang untuk mulai chat baru</p>
            </div>

            {{-- Messages --}}
            <template x-for="(msg, idx) in messages" :key="msg.id">
                <div class="message-enter" :class="msg.sender.id == {{ auth()->id() }} ? 'flex justify-end' : 'flex justify-start'">
                    <div class="max-w-[75%] lg:max-w-[60%]"
                         :class="msg.sender.id == {{ auth()->id() }}
                            ? 'bg-emerald-500 text-white rounded-2xl rounded-br-md px-4 py-2.5 shadow-sm'
                            : 'bg-white text-gray-800 rounded-2xl rounded-bl-md px-4 py-2.5 shadow-sm border border-gray-100'">
                        <p class="text-sm leading-relaxed whitespace-pre-wrap" x-text="msg.body"></p>
                        <div class="flex items-center justify-end mt-1 space-x-1">
                            <span class="text-[10px] opacity-70" x-text="formatTime(msg.created_at)"></span>
                            <template x-if="msg.sender.id == {{ auth()->id() }}">
                                <svg x-show="msg.is_read" class="w-3.5 h-3.5 opacity-70" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M18 7l-1.41-1.41-6.34 6.34 1.41 1.41L18 7zm4.24-1.41L11.66 16.17 7.48 12l-1.41 1.41L11.66 19l12-12-1.42-1.41zM.41 13.41L6 19l1.41-1.41L1.83 12 .41 13.41z"/>
                                </svg>
                            </template>
                        </div>
                    </div>
                </div>
            </template>

            {{-- Typing Indicator --}}
            <div x-show="typingUser" x-cloak class="flex justify-start">
                <div class="bg-white rounded-2xl rounded-bl-md px-4 py-3 shadow-sm border border-gray-100">
                    <div class="flex space-x-1">
                        <div class="typing-dot w-2 h-2 bg-gray-400 rounded-full"></div>
                        <div class="typing-dot w-2 h-2 bg-gray-400 rounded-full"></div>
                        <div class="typing-dot w-2 h-2 bg-gray-400 rounded-full"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Input Area --}}
        <div x-show="activeConversation" class="px-4 py-3 bg-white border-t border-gray-100">
            <form @submit.prevent="sendMessage()" class="flex items-end space-x-3">
                <div class="flex-1 relative">
                    <textarea x-model="newMessage" @keydown.enter.prevent="if (!$event.shiftKey) sendMessage()"
                        @input="emitTyping()"
                        rows="1" placeholder="Ketik pesan..."
                        class="w-full resize-none border-0 bg-gray-50 rounded-2xl px-4 py-3 pr-12 text-sm focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all duration-200 max-h-32 overflow-y-auto"
                        x-ref="messageInput"
                        @input="$el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px'"></textarea>
                </div>
                <button type="submit" :disabled="!newMessage.trim()"
                    class="btn-press flex-shrink-0 w-11 h-11 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-full flex items-center justify-center shadow-lg shadow-emerald-200 hover:shadow-emerald-300 transition-all duration-200 disabled:opacity-40 disabled:shadow-none disabled:cursor-not-allowed">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function chatApp() {
    return {
        conversations: @json($conversations ?? []),
        activeConversation: {{ isset($conversation) ? $conversation->id : 'null' }},
        activeName: '{{ isset($conversation) ? $conversation->getOtherParticipant(auth()->user())->name ?? "" : "" }}',
        isOnline: false,
        messages: @json(isset($messages) ? $messages->items() : []),
        newMessage: '',
        typingUser: null,
        typingTimeout: null,
        searchQuery: '',
        searchResults: [],
        unreadTotal: 0,
        echoChannel: null,

        init() {
            this.calculateUnread();
            if (this.activeConversation) {
                this.subscribeToConversation(this.activeConversation);
                this.$nextTick(() => this.scrollToBottom());
            }
            this.listenForNotifications();
        },

        calculateUnread() {
            this.unreadTotal = this.conversations.reduce((sum, c) => sum + (c.unread_count || 0), 0);
        },

        openConversation(id) {
            window.location.href = `/blade/conversations/${id}`;
        },

        closeConversation() {
            this.activeConversation = null;
            window.location.href = '/blade/conversations';
        },

        subscribeToConversation(id) {
            if (this.echoChannel) {
                Echo.leave(this.echoChannel);
            }
            this.echoChannel = `conversation.${id}`;

            Echo.join(`conversation.${id}`)
                .listen('MessageSent', (e) => {
                    this.messages.push(e);
                    this.$nextTick(() => this.scrollToBottom());
                    // Mark as read
                    fetch(`/blade/conversations/${id}/read`, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }
                    });
                })
                .listen('TypingStarted', (e) => {
                    this.typingUser = e.user_name;
                    clearTimeout(this.typingTimeout);
                    this.typingTimeout = setTimeout(() => { this.typingUser = null; }, 3000);
                });
        },

        listenForNotifications() {
            Echo.private(`user.{{ auth()->id() }}`)
                .listen('NotificationSent', (e) => {
                    this.unreadTotal++;
                    // Update conversation list jika ada
                    let conv = this.conversations.find(c => c.id == e.conversation_id);
                    if (conv) {
                        conv.unread_count = (conv.unread_count || 0) + 1;
                    }
                });
        },

        async sendMessage() {
            if (!this.newMessage.trim()) return;

            const body = this.newMessage;
            this.newMessage = '';
            this.$refs.messageInput.style.height = 'auto';

            try {
                const res = await fetch('{{ route("blade.messages.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        conversation_id: this.activeConversation,
                        body: body,
                    }),
                });
                const data = await res.json();
                if (data.message) {
                    this.messages.push(data.message);
                    this.$nextTick(() => this.scrollToBottom());
                }
            } catch (e) {
                console.error('Gagal kirim pesan:', e);
                this.newMessage = body; // Kembalikan pesan jika gagal
            }
        },

        emitTyping() {
            fetch('{{ route("blade.typing") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                },
                body: JSON.stringify({ conversation_id: this.activeConversation }),
            });
        },

        async searchUsers() {
            if (this.searchQuery.length < 2) {
                this.searchResults = [];
                return;
            }
            try {
                const res = await fetch(`{{ route("blade.users.search") }}?q=${encodeURIComponent(this.searchQuery)}`, {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();
                this.searchResults = data.users || [];
            } catch (e) {
                this.searchResults = [];
            }
        },

        async startChat(userId) {
            try {
                const res = await fetch('{{ route("blade.conversations.start") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ user_id: userId }),
                });
                const data = await res.json();
                if (data.conversation) {
                    window.location.href = `/blade/conversations/${data.conversation.id}`;
                }
            } catch (e) {
                console.error(e);
            }
        },

        scrollToBottom() {
            const container = this.$refs.messagesContainer;
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        },

        formatTime(dateStr) {
            const date = new Date(dateStr);
            return date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        },
    };
}
</script>
@endpush
@endsection
