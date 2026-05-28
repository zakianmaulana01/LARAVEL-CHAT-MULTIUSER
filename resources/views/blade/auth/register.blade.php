@extends('blade.layouts.app')
@section('title', 'Daftar')

@section('content')
<div class="min-h-full flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 animate-fade-in">
        <div class="text-center">
            <div class="mx-auto w-16 h-16 bg-gradient-to-br from-emerald-400 to-teal-600 rounded-2xl flex items-center justify-center shadow-lg shadow-emerald-200">
                <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
            </div>
            <h2 class="mt-6 text-3xl font-bold tracking-tight text-gray-900">Buat Akun Baru</h2>
            <p class="mt-2 text-sm text-gray-500">Daftar gratis dan mulai chat</p>
        </div>

        <form method="POST" action="{{ route('blade.register') }}" class="mt-8 space-y-5">
            @csrf
            <div class="space-y-4">
                <div class="animate-fade-in animate-fade-in-delay-1">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                    <input id="name" name="name" type="text" required value="{{ old('name') }}"
                        class="appearance-none relative block w-full px-4 py-3 border border-gray-200 rounded-xl placeholder-gray-400 text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 bg-white shadow-sm"
                        placeholder="Nama lengkap Anda">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="animate-fade-in animate-fade-in-delay-2">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input id="email" name="email" type="email" required value="{{ old('email') }}"
                        class="appearance-none relative block w-full px-4 py-3 border border-gray-200 rounded-xl placeholder-gray-400 text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 bg-white shadow-sm"
                        placeholder="nama@email.com">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="animate-fade-in animate-fade-in-delay-3">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input id="password" name="password" type="password" required
                        class="appearance-none relative block w-full px-4 py-3 border border-gray-200 rounded-xl placeholder-gray-400 text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 bg-white shadow-sm"
                        placeholder="Minimal 8 karakter">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="animate-fade-in animate-fade-in-delay-4">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required
                        class="appearance-none relative block w-full px-4 py-3 border border-gray-200 rounded-xl placeholder-gray-400 text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 bg-white shadow-sm"
                        placeholder="Ulangi password">
                </div>
            </div>

            <div class="animate-fade-in animate-fade-in-delay-4">
                <button type="submit" class="btn-press group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-semibold rounded-xl text-white bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 shadow-lg shadow-emerald-200 transition-all duration-200">
                    Daftar Sekarang
                </button>
            </div>
        </form>

        <p class="text-center text-sm text-gray-500">
            Sudah punya akun?
            <a href="{{ route('blade.login') }}" class="font-medium text-emerald-600 hover:text-emerald-500 transition-colors">
                Masuk di sini
            </a>
        </p>
    </div>
</div>
@endsection
