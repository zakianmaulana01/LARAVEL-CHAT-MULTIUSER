@extends('blade.layouts.app')
@section('title', 'Masuk')

@section('content')
<div class="min-h-full flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 animate-fade-in">
        {{-- Logo & Header --}}
        <div class="text-center">
            <div class="mx-auto w-16 h-16 bg-gradient-to-br from-emerald-400 to-teal-600 rounded-2xl flex items-center justify-center shadow-lg shadow-emerald-200">
                <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
            </div>
            <h2 class="mt-6 text-3xl font-bold tracking-tight text-gray-900">Selamat Datang</h2>
            <p class="mt-2 text-sm text-gray-500">Masuk ke akun Anda untuk mulai chat</p>
        </div>

        {{-- Error Messages --}}
        @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm animate-fade-in">
            {{ session('error') }}
        </div>
        @endif

        {{-- Login Form --}}
        <form method="POST" action="{{ route('blade.login') }}" class="mt-8 space-y-5">
            @csrf
            <div class="space-y-4">
                <div class="animate-fade-in animate-fade-in-delay-1">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input id="email" name="email" type="email" autocomplete="email" required
                        value="{{ old('email') }}"
                        class="appearance-none relative block w-full px-4 py-3 border border-gray-200 rounded-xl placeholder-gray-400 text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 bg-white shadow-sm"
                        placeholder="nama@email.com">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="animate-fade-in animate-fade-in-delay-2">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input id="password" name="password" type="password" autocomplete="current-password" required
                        class="appearance-none relative block w-full px-4 py-3 border border-gray-200 rounded-xl placeholder-gray-400 text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 bg-white shadow-sm"
                        placeholder="Masukkan password">
                </div>
            </div>

            <div class="flex items-center justify-between animate-fade-in animate-fade-in-delay-3">
                <label class="flex items-center">
                    <input type="checkbox" name="remember" class="w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                    <span class="ml-2 text-sm text-gray-600">Ingat saya</span>
                </label>
            </div>

            <div class="animate-fade-in animate-fade-in-delay-4">
                <button type="submit" class="btn-press group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-semibold rounded-xl text-white bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 shadow-lg shadow-emerald-200 transition-all duration-200">
                    Masuk
                </button>
            </div>
        </form>

        <p class="text-center text-sm text-gray-500 animate-fade-in animate-fade-in-delay-4">
            Belum punya akun?
            <a href="{{ route('blade.register') }}" class="font-medium text-emerald-600 hover:text-emerald-500 transition-colors">
                Daftar sekarang
            </a>
        </p>
    </div>
</div>
@endsection
