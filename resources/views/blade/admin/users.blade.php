@extends('blade.layouts.app')
@section('title', 'Kelola Pengguna')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Kelola Pengguna</h1>
                    <p class="text-sm text-gray-500 mt-1">{{ $users->total() }} pengguna terdaftar</p>
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

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Pengguna</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Pesan</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Terakhir Online</th>
                            <th class="px-6 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach($users as $user)
                        <tr class="hover:bg-gray-50 transition-colors animate-fade-in" style="animation-delay: {{ $loop->index * 30 }}ms">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center text-white text-xs font-bold">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->role === 'superadmin' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ number_format($user->messages_count) }}</td>
                            <td class="px-6 py-4">
                                @if($user->is_banned)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Diblokir</span>
                                @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Aktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $user->last_seen ? $user->last_seen->diffForHumans() : 'Belum pernah' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if(!$user->isSuperadmin())
                                <div class="flex items-center justify-end space-x-2">
                                    <form method="POST" action="{{ route('blade.admin.users.ban', $user) }}">
                                        @csrf
                                        <button type="submit" class="btn-press px-3 py-1.5 text-xs font-medium rounded-lg {{ $user->is_banned ? 'bg-green-50 text-green-700 hover:bg-green-100' : 'bg-yellow-50 text-yellow-700 hover:bg-yellow-100' }} transition-colors">
                                            {{ $user->is_banned ? 'Aktifkan' : 'Blokir' }}
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('blade.admin.users.destroy', $user) }}" onsubmit="return confirm('Yakin hapus user ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-press px-3 py-1.5 text-xs font-medium rounded-lg bg-red-50 text-red-700 hover:bg-red-100 transition-colors">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
