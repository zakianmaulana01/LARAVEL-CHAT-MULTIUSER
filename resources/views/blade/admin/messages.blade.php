@extends('blade.layouts.app')
@section('title', 'Moderasi Pesan')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Moderasi Pesan</h1>
                    <p class="text-sm text-gray-500 mt-1">Pantau dan kelola pesan pengguna</p>
                </div>
                <a href="{{ route('blade.admin.dashboard') }}" class="btn-press text-sm text-gray-600 hover:text-gray-900 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if(session('success'))
        <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm animate-fade-in">
            {{ session('success') }}
        </div>
        @endif

        <div class="space-y-3">
            @foreach($messages as $message)
            <div class="animate-fade-in bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:shadow-md transition-shadow" style="animation-delay: {{ $loop->index * 30 }}ms">
                <div class="flex items-start justify-between">
                    <div class="flex items-start space-x-3 flex-1 min-w-0">
                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                            {{ strtoupper(substr($message->sender->name ?? 'U', 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center space-x-2">
                                <span class="text-sm font-semibold text-gray-900">{{ $message->sender->name ?? 'Deleted User' }}</span>
                                <span class="text-xs text-gray-400">{{ $message->created_at->format('d M Y, H:i') }}</span>
                                @if($message->deleted_by_superadmin)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Dihapus</span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-700 mt-1 {{ $message->deleted_by_superadmin ? 'line-through opacity-50' : '' }}">{{ $message->body }}</p>
                        </div>
                    </div>
                    @if(!$message->deleted_by_superadmin)
                    <form method="POST" action="{{ route('blade.admin.messages.destroy', $message) }}" onsubmit="return confirm('Yakin hapus pesan ini?')" class="flex-shrink-0 ml-3">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-press p-2 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $messages->links() }}
        </div>
    </div>
</div>
@endsection
