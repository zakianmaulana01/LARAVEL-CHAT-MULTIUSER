@extends('blade.layouts.app')
@section('title', 'Chat')

@section('content')
<div id="chat-app" class="h-screen flex overflow-hidden bg-gray-100">
    {{-- Sidebar --}}
    <div id="sidebar" class="w-full md:w-96 lg:w-[420px] flex flex-col border-r border-gray-200 bg-white">

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
                <div class="relative">
                    <button id="btn-notif" class="btn-press p-2 rounded-full hover:bg-gray-100 transition-colors relative">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span id="unread-badge" class="badge-pop absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center font-bold hidden-el"></span>
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

                {{-- AI Chat Button --}}
                <a href="{{ route('blade.ai.chat') }}" class="btn-press p-2 rounded-full hover:bg-gray-100 transition-colors" title="AI Assistant">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714a2.25 2.25 0 00.659 1.591L19 14.5M14.25 3.104c.251.023.501.05.75.082M19 14.5l-2.47 2.47a2.25 2.25 0 00-.659 1.591v1.689m-4.621 0h4.621m-4.621 0L9.25 22.5m4.621-2.25L16.5 22.5"/>
                    </svg>
                </a>

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
                <input type="text" id="search-input"
                    placeholder="Cari atau mulai chat baru..."
                    class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border-0 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all duration-200">
            </div>

            {{-- Search Results --}}
            <div id="search-results" class="mt-2 bg-white border border-gray-100 rounded-xl shadow-lg overflow-hidden hidden-el"></div>
        </div>

        {{-- Conversation List --}}
        <div class="flex-1 overflow-y-auto scrollbar-thin">
            @forelse($conversations ?? [] as $index => $conv)
            <button data-conv-id="{{ $conv->id }}"
                class="conv-item btn-press w-full flex items-center px-5 py-3.5 hover:bg-gray-50 transition-all duration-150 border-b border-gray-50 animate-fade-in"
                style="animation-delay: {{ $index * 50 }}ms">

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
    <div id="chat-window" class="flex-1 flex flex-col bg-gray-50 hidden md:flex">

        {{-- Chat Header --}}
        <div id="chat-header" class="flex items-center px-5 py-3.5 bg-white border-b border-gray-100 shadow-sm hidden-el">
            <button id="btn-back" class="md:hidden btn-press mr-3 p-1 rounded-full hover:bg-gray-100">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center text-white font-bold text-sm shadow-sm">
                <span id="header-avatar"></span>
            </div>
            <div class="ml-3">
                <h3 id="header-name" class="text-sm font-semibold text-gray-900"></h3>
                <p id="header-status" class="text-xs text-gray-400">
                    <span id="typing-status" class="text-emerald-500 italic hidden-el"></span>
                    <span id="online-status">Offline</span>
                </p>
            </div>
        </div>

        {{-- Messages Area --}}
        <div id="messages-container" class="flex-1 overflow-y-auto px-4 py-4 space-y-2 scrollbar-thin" style="background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 50%, #f0f9ff 100%);">
            {{-- Empty State --}}
            <div id="empty-state" class="flex flex-col items-center justify-center h-full text-gray-400">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-12 h-12 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                </div>
                <p class="text-base font-medium text-gray-500">Pilih percakapan</p>
                <p class="text-sm mt-1">atau cari seseorang untuk mulai chat baru</p>
            </div>

            {{-- Messages will be rendered here by jQuery --}}
            <div id="messages-list"></div>

            {{-- Typing Indicator --}}
            <div id="typing-indicator" class="flex justify-start hidden-el">
                <div class="bg-white rounded-2xl rounded-bl-md px-4 py-3 shadow-sm border border-gray-100">
                    <div class="flex space-x-1">
                        <div class="typing-dot w-2 h-2 bg-gray-400 rounded-full"></div>
                        <div class="typing-dot w-2 h-2 bg-gray-400 rounded-full"></div>
                        <div class="typing-dot w-2 h-2 bg-gray-400 rounded-full"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- File Preview Area --}}
        <div id="file-preview-area" class="px-4 pt-2 bg-white border-t border-gray-100 hidden-el">
            <div class="flex items-center space-x-3 pb-2">
                <div id="file-preview-content" class="flex items-center space-x-2 bg-gray-50 rounded-xl px-3 py-2 flex-1 min-w-0"></div>
                <button id="btn-remove-file" type="button" class="flex-shrink-0 w-6 h-6 rounded-full bg-gray-200 hover:bg-gray-300 flex items-center justify-center text-gray-600 text-xs transition-colors">&times;</button>
            </div>
        </div>

        {{-- Input Area --}}
        <div id="input-area" class="px-4 py-3 bg-white border-t border-gray-100 hidden-el">
            <form id="message-form" class="flex items-end space-x-2">
                {{-- Paperclip Button --}}
                <button type="button" id="btn-attach" class="btn-press flex-shrink-0 w-10 h-10 rounded-full hover:bg-gray-100 flex items-center justify-center text-gray-500 transition-colors" title="Lampirkan file">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                    </svg>
                </button>
                {{-- Hidden file input --}}
                <input type="file" id="file-input" accept="image/*,.pdf,.doc,.docx,.zip,.txt" class="hidden">

                <div class="flex-1 relative">
                    <textarea id="message-input" rows="1" placeholder="Ketik pesan..."
                        class="w-full resize-none border-0 bg-gray-50 rounded-2xl px-4 py-3 pr-12 text-sm focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all duration-200 max-h-32 overflow-y-auto"></textarea>
                </div>
                <button type="submit" id="btn-send"
                    class="btn-press flex-shrink-0 w-11 h-11 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-full flex items-center justify-center shadow-lg shadow-emerald-200 hover:shadow-emerald-300 transition-all duration-200 disabled:opacity-40 disabled:shadow-none disabled:cursor-not-allowed" disabled>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</div>

<style>
.msg-actions {
    display: none;
    position: absolute;
    top: -32px;
    right: 0;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 2px 4px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.12);
    z-index: 10;
    white-space: nowrap;
}
.msg-actions button {
    background: none;
    border: none;
    cursor: pointer;
    padding: 3px 5px;
    font-size: 13px;
    border-radius: 4px;
    transition: background 0.15s;
}
.msg-actions button:hover {
    background: #f3f4f6;
}
.msg-bubble-wrapper {
    position: relative;
    display: inline-block;
    max-width: 100%;
}
.msg-bubble-wrapper:hover .msg-actions {
    display: flex;
    align-items: center;
}
</style>

@push('scripts')
<script>
(function($) {
    'use strict';

    var ChatApp = {
        // State
        activeConversation: {{ isset($conversation) ? $conversation->id : 'null' }},
        activeName: '{{ isset($conversation) ? addslashes($conversation->getOtherParticipant(auth()->user())->name ?? "") : "" }}',
        messages: @json(isset($messages) ? $messages->items() : []),
        unreadTotal: 0,
        typingTimeout: null,
        echoChannel: null,
        searchDebounce: null,
        currentUserId: {{ auth()->id() }},
        selectedFile: null,

        init: function() {
            this.bindEvents();
            this.calculateUnread();

            if (this.activeConversation) {
                this.showConversation();
                this.renderMessages();
                this.subscribeToConversation(this.activeConversation);
                this.scrollToBottom();
            }

            this.listenForNotifications();
        },

        bindEvents: function() {
            var self = this;

            // Conversation click
            $(document).on('click', '.conv-item', function() {
                var id = $(this).data('conv-id');
                window.location.href = '/blade/conversations/' + id;
            });

            // Search input with debounce
            $('#search-input').on('input', function() {
                clearTimeout(self.searchDebounce);
                self.searchDebounce = setTimeout(function() {
                    self.searchUsers();
                }, 300);
            });

            // Send message
            $('#message-form').on('submit', function(e) {
                e.preventDefault();
                self.sendMessage();
            });

            // Enter to send (shift+enter for newline)
            $('#message-input').on('keydown', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    self.sendMessage();
                }
            });

            // Auto-resize textarea
            $('#message-input').on('input', function() {
                this.style.height = 'auto';
                this.style.height = this.scrollHeight + 'px';
                // Enable/disable send button based on text or file
                $('#btn-send').prop('disabled', !$(this).val().trim() && !self.selectedFile);
                // Emit typing
                self.emitTyping();
            });

            // Back button (mobile)
            $('#btn-back').on('click', function() {
                window.location.href = '/blade/conversations';
            });

            // Attach file button
            $('#btn-attach').on('click', function() {
                $('#file-input').click();
            });

            // File selected
            $('#file-input').on('change', function() {
                var file = this.files[0];
                if (!file) return;
                self.selectedFile = file;
                self.showFilePreview(file);
                $('#btn-send').prop('disabled', false);
            });

            // Remove file
            $('#btn-remove-file').on('click', function() {
                self.clearSelectedFile();
            });

            // Edit message (save)
            $(document).on('click', '.btn-edit-save', function() {
                var $bubble = $(this).closest('[data-msg-id]');
                var msgId = $bubble.data('msg-id');
                var newBody = $bubble.find('.edit-textarea').val().trim();
                if (!newBody) return;
                self.saveEdit(msgId, newBody, $bubble);
            });

            // Edit message (cancel)
            $(document).on('click', '.btn-edit-cancel', function() {
                var $bubble = $(this).closest('[data-msg-id]');
                var originalBody = $bubble.data('original-body');
                self.cancelEdit($bubble, originalBody);
            });

            // Long-press for mobile action menu
            var longPressTimer = null;
            $(document).on('touchstart', '[data-msg-id]', function() {
                var $el = $(this);
                longPressTimer = setTimeout(function() {
                    $el.find('.msg-actions').css('display', 'flex');
                }, 500);
            });
            $(document).on('touchend touchcancel', '[data-msg-id]', function() {
                clearTimeout(longPressTimer);
            });
        },

        showFilePreview: function(file) {
            var isImage = /\.(jpg|jpeg|png|gif|webp)$/i.test(file.name);
            var html = '';
            if (isImage) {
                var url = URL.createObjectURL(file);
                html = '<img src="' + url + '" class="w-10 h-10 rounded-lg object-cover flex-shrink-0">' +
                       '<span class="text-xs text-gray-600 truncate">' + this.escapeHtml(file.name) + '</span>';
            } else {
                html = '<span class="text-lg">📄</span>' +
                       '<span class="text-xs text-gray-600 truncate">' + this.escapeHtml(file.name) + '</span>';
            }
            $('#file-preview-content').html(html);
            $('#file-preview-area').removeClass('hidden-el');
        },

        clearSelectedFile: function() {
            this.selectedFile = null;
            $('#file-input').val('');
            $('#file-preview-area').addClass('hidden-el');
            $('#file-preview-content').empty();
            // Update send button
            $('#btn-send').prop('disabled', !$('#message-input').val().trim());
        },

        calculateUnread: function() {
            var conversations = @json($conversations ?? []);
            this.unreadTotal = 0;
            for (var i = 0; i < conversations.length; i++) {
                this.unreadTotal += (conversations[i].unread_count || 0);
            }
            this.updateUnreadBadge();
        },

        updateUnreadBadge: function() {
            if (this.unreadTotal > 0) {
                $('#unread-badge').text(this.unreadTotal).removeClass('hidden-el');
            } else {
                $('#unread-badge').addClass('hidden-el');
            }
        },

        showConversation: function() {
            // On mobile: hide sidebar, show chat window
            $('#sidebar').addClass('hidden md:flex');
            $('#chat-window').removeClass('hidden').addClass('flex');
            $('#chat-header').removeClass('hidden-el');
            $('#input-area').removeClass('hidden-el');
            $('#empty-state').hide();
            $('#messages-list').show();

            // Update header
            $('#header-avatar').text(this.activeName ? this.activeName.charAt(0).toUpperCase() : '');
            $('#header-name').text(this.activeName);

            // Highlight active conversation
            $('.conv-item').removeClass('bg-emerald-50 border-l-4 border-l-emerald-500');
            $('.conv-item[data-conv-id="' + this.activeConversation + '"]').addClass('bg-emerald-50 border-l-4 border-l-emerald-500');
        },

        renderMessages: function() {
            var self = this;
            var $list = $('#messages-list');
            $list.empty();

            $.each(this.messages, function(idx, msg) {
                $list.append(self.createMessageBubble(msg));
            });
        },

        createMessageBubble: function(msg) {
            var self = this;
            var isMine = msg.sender.id == this.currentUserId;
            var alignClass = isMine ? 'flex justify-end' : 'flex justify-start';
            var bubbleClass = isMine
                ? 'bg-emerald-500 text-white rounded-2xl rounded-br-md px-4 py-2.5 shadow-sm'
                : 'bg-white text-gray-800 rounded-2xl rounded-bl-md px-4 py-2.5 shadow-sm border border-gray-100';

            var time = this.formatTime(msg.created_at);

            // Read receipt
            var readCheck = '';
            if (isMine) {
                if (msg.is_read) {
                    // Double check — emerald tinted
                    readCheck = '<svg class="w-3.5 h-3.5 text-emerald-200" fill="currentColor" viewBox="0 0 24 24"><path d="M18 7l-1.41-1.41-6.34 6.34 1.41 1.41L18 7zm4.24-1.41L11.66 16.17 7.48 12l-1.41 1.41L11.66 19l12-12-1.42-1.41zM.41 13.41L6 19l1.41-1.41L1.83 12 .41 13.41z"/></svg>';
                } else {
                    // Single check — gray, sent
                    readCheck = '<svg class="w-3 h-3 opacity-50" fill="currentColor" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>';
                }
            }

            // Message body content
            var bodyHtml = '';
            if (msg.deleted_by_sender) {
                bodyHtml = '<em class="text-xs opacity-60">Pesan ini telah dihapus</em>';
            } else {
                // File attachment
                if (msg.file_path) {
                    var ext = msg.file_path.split('.').pop().toLowerCase();
                    var imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                    if (imageExts.indexOf(ext) !== -1) {
                        bodyHtml += '<a href="/storage/' + msg.file_path + '" target="_blank">' +
                            '<img src="/storage/' + msg.file_path + '" class="max-w-[200px] rounded-xl mb-1 cursor-pointer" loading="lazy" style="display:block;">' +
                        '</a>';
                    } else {
                        var fileName = msg.file_path.split('/').pop();
                        bodyHtml += '<a href="/storage/' + msg.file_path + '" download class="flex items-center space-x-2 mb-1 opacity-90 hover:opacity-100">' +
                            '<span class="text-lg">📄</span>' +
                            '<span class="text-xs underline">' + self.escapeHtml(fileName) + '</span>' +
                        '</a>';
                    }
                }
                // Text body
                if (msg.body) {
                    bodyHtml += '<p class="msg-body-text text-sm leading-relaxed whitespace-pre-wrap">' + this.escapeHtml(msg.body) + '</p>';
                }
                // Edited label
                if (msg.edited_at) {
                    bodyHtml += '<span class="text-[10px] opacity-50 italic"> (diedit)</span>';
                }
            }

            // Action buttons (only for own messages, not deleted)
            var actionsHtml = '';
            if (isMine && !msg.deleted_by_sender) {
                var now = new Date();
                var sentAt = new Date(msg.created_at);
                var canEdit = (now - sentAt) < 5 * 60 * 1000;
                if (canEdit) {
                    actionsHtml = '<div class="msg-actions">' +
                        '<button class="btn-msg-edit" title="Edit">✏️</button>' +
                        '<button class="btn-msg-delete" title="Hapus">🗑️</button>' +
                    '</div>';
                }
            }

            return $('<div class="message-enter ' + alignClass + '">' +
                '<div class="msg-bubble-wrapper" data-msg-id="' + msg.id + '" data-original-body="' + this.escapeHtml(msg.body || '') + '">' +
                    actionsHtml +
                    '<div class="max-w-[75%] lg:max-w-[60%] ' + bubbleClass + '">' +
                        '<div class="msg-content">' + bodyHtml + '</div>' +
                        '<div class="flex items-center justify-end mt-1 space-x-1">' +
                            '<span class="text-[10px] opacity-70">' + time + '</span>' +
                            readCheck +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>');
        },

        updateMessageBubble: function(msg) {
            var $wrapper = $('[data-msg-id="' + msg.id + '"]');
            if (!$wrapper.length) return;

            var $content = $wrapper.find('.msg-content');
            var bodyHtml = '';

            if (msg.deleted_by_sender) {
                bodyHtml = '<em class="text-xs opacity-60">Pesan ini telah dihapus</em>';
                $wrapper.find('.msg-actions').remove();
            } else {
                if (msg.file_path) {
                    var ext = msg.file_path.split('.').pop().toLowerCase();
                    var imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                    if (imageExts.indexOf(ext) !== -1) {
                        bodyHtml += '<a href="/storage/' + msg.file_path + '" target="_blank">' +
                            '<img src="/storage/' + msg.file_path + '" class="max-w-[200px] rounded-xl mb-1 cursor-pointer" loading="lazy" style="display:block;">' +
                        '</a>';
                    } else {
                        var fileName = msg.file_path.split('/').pop();
                        bodyHtml += '<a href="/storage/' + msg.file_path + '" download class="flex items-center space-x-2 mb-1 opacity-90 hover:opacity-100">' +
                            '<span class="text-lg">📄</span>' +
                            '<span class="text-xs underline">' + this.escapeHtml(fileName) + '</span>' +
                        '</a>';
                    }
                }
                if (msg.body) {
                    bodyHtml += '<p class="msg-body-text text-sm leading-relaxed whitespace-pre-wrap">' + this.escapeHtml(msg.body) + '</p>';
                }
                if (msg.edited_at) {
                    bodyHtml += '<span class="text-[10px] opacity-50 italic"> (diedit)</span>';
                }
            }

            $content.html(bodyHtml);
            $wrapper.data('original-body', msg.body || '');
        },

        saveEdit: function(msgId, newBody, $wrapper) {
            var self = this;
            $.ajax({
                url: '/blade/messages/' + msgId,
                method: 'PATCH',
                contentType: 'application/json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr('content'),
                    'Accept': 'application/json'
                },
                data: JSON.stringify({ body: newBody }),
                success: function(data) {
                    var msg = data.message || data;
                    var bodyHtml = '<p class="msg-body-text text-sm leading-relaxed whitespace-pre-wrap">' + self.escapeHtml(msg.body || newBody) + '</p>';
                    if (msg.edited_at || data.edited_at) {
                        bodyHtml += '<span class="text-[10px] opacity-50 italic"> (diedit)</span>';
                    }
                    $wrapper.find('.msg-content').html(bodyHtml);
                    $wrapper.data('original-body', newBody);
                },
                error: function() {
                    self.cancelEdit($wrapper, $wrapper.data('original-body'));
                }
            });
        },

        cancelEdit: function($wrapper, originalBody) {
            var bodyHtml = '<p class="msg-body-text text-sm leading-relaxed whitespace-pre-wrap">' + this.escapeHtml(originalBody || '') + '</p>';
            $wrapper.find('.msg-content').html(bodyHtml);
        },

        deleteMessage: function(msgId, $wrapper) {
            var self = this;
            if (!confirm('Hapus pesan ini?')) return;
            $.ajax({
                url: '/blade/messages/' + msgId,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr('content'),
                    'Accept': 'application/json'
                },
                success: function() {
                    $wrapper.find('.msg-content').html('<em class="text-xs opacity-60">Pesan ini telah dihapus</em>');
                    $wrapper.find('.msg-actions').remove();
                }
            });
        },

        escapeHtml: function(text) {
            var map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
            return text ? text.replace(/[&<>"']/g, function(m) { return map[m]; }) : '';
        },

        formatTime: function(dateStr) {
            var date = new Date(dateStr);
            return date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        },

        scrollToBottom: function() {
            var container = document.getElementById('messages-container');
            if (container) {
                setTimeout(function() {
                    container.scrollTop = container.scrollHeight;
                }, 50);
            }
        },

        subscribeToConversation: function(id) {
            var self = this;
            if (this.echoChannel) {
                Echo.leave(this.echoChannel);
            }
            this.echoChannel = 'conversation.' + id;

            Echo.join('conversation.' + id)
                .listen('MessageSent', function(e) {
                    self.messages.push(e);
                    $('#messages-list').append(self.createMessageBubble(e));
                    self.scrollToBottom();
                    // Mark as read
                    $.ajax({
                        url: '/blade/conversations/' + id + '/read',
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr('content') }
                    });
                })
                .listen('MessageUpdated', function(e) {
                    self.updateMessageBubble(e.message);
                })
                .listen('TypingStarted', function(e) {
                    $('#typing-indicator').removeClass('hidden-el');
                    $('#typing-status').text(e.user_name + ' sedang mengetik...').removeClass('hidden-el');
                    $('#online-status').addClass('hidden-el');
                    clearTimeout(self.typingTimeout);
                    self.typingTimeout = setTimeout(function() {
                        $('#typing-indicator').addClass('hidden-el');
                        $('#typing-status').addClass('hidden-el');
                        $('#online-status').removeClass('hidden-el');
                    }, 3000);
                });
        },

        listenForNotifications: function() {
            var self = this;
            Echo.private('user.{{ auth()->id() }}')
                .listen('NotificationSent', function(e) {
                    self.unreadTotal++;
                    self.updateUnreadBadge();
                });
        },

        sendMessage: function() {
            var self = this;
            var body = $('#message-input').val().trim();

            // Require either body or file
            if (!body && !this.selectedFile) return;

            $('#message-input').val('').css('height', 'auto');
            $('#btn-send').prop('disabled', true);

            var file = this.selectedFile;
            this.clearSelectedFile();

            if (file) {
                // Use FormData for file upload
                var formData = new FormData();
                formData.append('conversation_id', self.activeConversation);
                if (body) formData.append('body', body);
                formData.append('file', file);

                $.ajax({
                    url: '{{ route("blade.messages.store") }}',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr('content'),
                        'Accept': 'application/json'
                    },
                    success: function(data) {
                        if (data.message) {
                            self.messages.push(data.message);
                            $('#messages-list').append(self.createMessageBubble(data.message));
                            self.scrollToBottom();
                        }
                    },
                    error: function() {
                        $('#message-input').val(body);
                        $('#btn-send').prop('disabled', false);
                    }
                });
            } else {
                $.ajax({
                    url: '{{ route("blade.messages.store") }}',
                    method: 'POST',
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr('content'),
                        'Accept': 'application/json'
                    },
                    data: JSON.stringify({
                        conversation_id: self.activeConversation,
                        body: body
                    }),
                    success: function(data) {
                        if (data.message) {
                            self.messages.push(data.message);
                            $('#messages-list').append(self.createMessageBubble(data.message));
                            self.scrollToBottom();
                        }
                    },
                    error: function() {
                        $('#message-input').val(body);
                        $('#btn-send').prop('disabled', false);
                    }
                });
            }
        },

        emitTyping: function() {
            if (!this.activeConversation) return;
            $.ajax({
                url: '{{ route("blade.typing") }}',
                method: 'POST',
                contentType: 'application/json',
                headers: { 'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr('content') },
                data: JSON.stringify({ conversation_id: this.activeConversation })
            });
        },

        searchUsers: function() {
            var query = $('#search-input').val().trim();
            if (query.length < 2) {
                $('#search-results').addClass('hidden-el').empty();
                return;
            }

            $.ajax({
                url: '{{ route("blade.users.search") }}',
                method: 'GET',
                data: { q: query },
                headers: { 'Accept': 'application/json' },
                success: function(data) {
                    var users = data.users || [];
                    if (users.length === 0) {
                        $('#search-results').addClass('hidden-el').empty();
                        return;
                    }
                    var html = '';
                    $.each(users, function(i, user) {
                        html += '<button data-user-id="' + user.id + '" class="search-user-item btn-press w-full flex items-center px-4 py-3 hover:bg-gray-50 transition-colors">' +
                            '<div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center text-white text-xs font-bold">' +
                                user.name.charAt(0).toUpperCase() +
                            '</div>' +
                            '<div class="ml-3 text-left">' +
                                '<p class="text-sm font-medium text-gray-900">' + ChatApp.escapeHtml(user.name) + '</p>' +
                                '<p class="text-xs text-gray-500">' + ChatApp.escapeHtml(user.email) + '</p>' +
                            '</div>' +
                        '</button>';
                    });
                    $('#search-results').html(html).removeClass('hidden-el');
                }
            });
        },

        startChat: function(userId) {
            $.ajax({
                url: '{{ route("blade.conversations.start") }}',
                method: 'POST',
                contentType: 'application/json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr('content'),
                    'Accept': 'application/json'
                },
                data: JSON.stringify({ user_id: userId }),
                success: function(data) {
                    if (data.conversation) {
                        window.location.href = '/blade/conversations/' + data.conversation.id;
                    }
                }
            });
        }
    };

    // Click handler for search results (event delegation)
    $(document).on('click', '.search-user-item', function() {
        var userId = $(this).data('user-id');
        ChatApp.startChat(userId);
    });

    // Edit button click
    $(document).on('click', '.btn-msg-edit', function(e) {
        e.stopPropagation();
        var $wrapper = $(this).closest('[data-msg-id]');
        var originalBody = $wrapper.data('original-body');
        var $content = $wrapper.find('.msg-content');

        // Replace content with textarea
        $content.html(
            '<textarea class="edit-textarea w-full text-sm bg-white bg-opacity-20 rounded-lg p-2 resize-none focus:outline-none" rows="2">' +
            $('<div>').text(originalBody).html() +
            '</textarea>' +
            '<div class="flex space-x-2 mt-1">' +
                '<button class="btn-edit-save text-xs bg-emerald-600 text-white px-3 py-1 rounded-lg hover:bg-emerald-700 transition-colors">Simpan</button>' +
                '<button class="btn-edit-cancel text-xs bg-gray-400 text-white px-3 py-1 rounded-lg hover:bg-gray-500 transition-colors">Batal</button>' +
            '</div>'
        );
        $content.find('.edit-textarea').focus();
    });

    // Delete button click
    $(document).on('click', '.btn-msg-delete', function(e) {
        e.stopPropagation();
        var $wrapper = $(this).closest('[data-msg-id]');
        var msgId = $wrapper.data('msg-id');
        ChatApp.deleteMessage(msgId, $wrapper);
    });

    // Initialize on document ready
    $(document).ready(function() {
        ChatApp.init();
    });

})(jQuery);
</script>
@endpush
@endsection
