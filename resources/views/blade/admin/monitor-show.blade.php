@extends('blade.layouts.admin')
@section('title', 'Monitor: ' . $conversation->participants->pluck('name')->join(' ↔ '))

@section('admin-content')
<div>
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <div class="flex items-center space-x-2 mb-1">
                <a href="{{ route('blade.admin.monitor') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Monitor Chat
                </a>
                <span class="text-gray-300">/</span>
                <span class="text-sm text-gray-700 font-medium">Detail</span>
            </div>
            <h1 class="text-xl font-bold text-gray-900">
                {{ $conversation->participants->pluck('name')->join(' ↔ ') ?: 'Percakapan #' . $conversation->id }}
            </h1>
            <p class="text-sm text-gray-500 mt-0.5">
                {{ $messages->total() }} pesan total &middot; Dimulai {{ $conversation->created_at->format('d M Y') }}
            </p>
        </div>
        <div class="flex items-center space-x-2">
            @foreach($conversation->participants as $p)
            <div class="flex items-center px-3 py-1.5 bg-gray-100 rounded-full text-sm text-gray-700">
                <div class="w-5 h-5 rounded-full bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center text-white text-xs font-bold mr-2">
                    {{ strtoupper(substr($p->name, 0, 1)) }}
                </div>
                {{ $p->name }}
                @if($p->is_banned)
                <span class="ml-1.5 text-xs text-red-500">(banned)</span>
                @endif
            </div>
            @endforeach
        </div>
    </div>

    {{-- Messages --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
            <span class="text-xs text-gray-500 font-medium uppercase tracking-wider">Riwayat Percakapan (Read-only)</span>
            <span class="text-xs text-gray-400">Halaman {{ $messages->currentPage() }} dari {{ $messages->lastPage() }}</span>
        </div>

        <div class="divide-y divide-gray-50 max-h-[65vh] overflow-y-auto" id="messages-container">
            @forelse($messages as $message)
            @php
                $isDeleted = $message->deleted_by_sender || $message->deleted_by_superadmin;
                $participants = $conversation->participants;
                $isFirst = $participants->isNotEmpty() && $message->sender_id === $participants->first()->id;
            @endphp
            <div class="px-5 py-3 hover:bg-gray-50/50 transition-colors flex items-start space-x-3 {{ $isDeleted ? 'opacity-60' : '' }}">
                {{-- Avatar --}}
                <div class="w-8 h-8 rounded-full flex-shrink-0 flex items-center justify-center text-white text-xs font-bold
                            {{ $isFirst ? 'bg-gradient-to-br from-emerald-400 to-teal-500' : 'bg-gradient-to-br from-blue-400 to-indigo-500' }}">
                    {{ strtoupper(substr($message->sender->name ?? '?', 0, 1)) }}
                </div>

                <div class="flex-1 min-w-0">
                    <div class="flex items-center space-x-2 mb-0.5">
                        <span class="text-sm font-semibold text-gray-900">{{ $message->sender->name ?? 'Deleted User' }}</span>
                        <span class="text-xs text-gray-400">{{ $message->created_at->format('d M Y, H:i') }}</span>
                        @if($message->deleted_by_superadmin)
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-red-100 text-red-700">dihapus admin</span>
                        @elseif($message->deleted_by_sender)
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-700">dihapus pengirim</span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-700 {{ $isDeleted ? 'line-through' : '' }}">
                        {{ $message->body }}
                    </p>
                </div>

                {{-- Delete button --}}
                @if(!$message->deleted_by_superadmin)
                <form method="POST" action="{{ route('blade.admin.messages.destroy', $message) }}"
                      onsubmit="return confirm('Yakin hapus pesan ini?')"
                      class="flex-shrink-0">
                    @csrf @method('DELETE')
                    <button type="submit"
                            title="Hapus pesan"
                            class="p-1.5 text-red-300 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </form>
                @else
                <div class="w-8 flex-shrink-0"></div>
                @endif
            </div>
            @empty
            <div class="px-6 py-12 text-center text-gray-400 text-sm">
                Belum ada pesan dalam percakapan ini.
            </div>
            @endforelse
        </div>

        @if($messages->hasPages())
        <div class="p-4 border-t border-gray-100 bg-gray-50">
            {{ $messages->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
