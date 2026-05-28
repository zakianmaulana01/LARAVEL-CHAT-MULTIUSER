@extends('blade.layouts.admin')
@section('title', 'Monitor Chat')

@section('admin-content')
<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Monitor Chat</h1>
        <p class="text-sm text-gray-500 mt-1">Pantau semua percakapan pengguna</p>
    </div>

    {{-- Search --}}
    <form method="GET" action="{{ route('blade.admin.monitor') }}" class="mb-5">
        <div class="flex gap-3">
            <input type="text" name="search" value="{{ $search }}"
                   placeholder="Cari berdasarkan nama peserta..."
                   class="flex-1 px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent bg-white">
            <button type="submit" class="px-4 py-2.5 bg-emerald-600 text-white text-sm font-medium rounded-xl hover:bg-emerald-700 transition-colors">
                Cari
            </button>
            @if($search)
            <a href="{{ route('blade.admin.monitor') }}" class="px-4 py-2.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-200 transition-colors">
                Reset
            </a>
            @endif
        </div>
    </form>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Peserta</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Pesan Terakhir</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Jumlah Pesan</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Waktu</th>
                        <th class="px-6 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($conversations as $conversation)
                    <tr class="hover:bg-gray-50 transition-colors cursor-pointer"
                        onclick="window.location='{{ route('blade.admin.monitor.show', $conversation) }}'">
                        <td class="px-6 py-4">
                            <div class="flex items-center -space-x-2">
                                @foreach($conversation->participants->take(3) as $participant)
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center text-white text-xs font-bold ring-2 ring-white">
                                    {{ strtoupper(substr($participant->name, 0, 1)) }}
                                </div>
                                @endforeach
                            </div>
                            <p class="mt-1.5 text-sm font-medium text-gray-900">
                                {{ $conversation->participants->pluck('name')->join(' ↔ ') ?: '(tidak ada peserta)' }}
                            </p>
                        </td>
                        <td class="px-6 py-4 max-w-xs">
                            @if($conversation->latestMessage)
                            <p class="text-sm text-gray-500 truncate">
                                <span class="font-medium text-gray-700">{{ $conversation->latestMessage->sender->name ?? '?' }}:</span>
                                {{ Str::limit($conversation->latestMessage->body, 60) }}
                            </p>
                            @else
                            <span class="text-xs text-gray-400 italic">Belum ada pesan</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                {{ number_format($conversation->messages_count) }} pesan
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                            {{ $conversation->updated_at->diffForHumans() }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('blade.admin.monitor.show', $conversation) }}"
                               onclick="event.stopPropagation()"
                               class="inline-flex items-center px-3 py-1.5 text-xs font-medium bg-teal-50 text-teal-700 hover:bg-teal-100 rounded-lg transition-colors">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Lihat
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-400 text-sm">
                            @if($search)
                                Tidak ada percakapan yang cocok dengan "<strong>{{ $search }}</strong>"
                            @else
                                Belum ada percakapan
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $conversations->appends(['search' => $search])->links() }}
    </div>
</div>
@endsection
