@extends('layouts.app')
@section('title', 'Masuk - KoSeSurabaya')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">
        {{-- Card --}}
        <div class="glass rounded-3xl p-8 shadow-2xl animate-slide-up">
            {{-- Header --}}
            <div class="text-center mb-8">
                <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-brand-400 to-brand-600 rounded-2xl flex items-center justify-center shadow-lg shadow-brand-500/20">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-white">Selamat Datang</h1>
                <p class="text-sm text-gray-400 mt-1">Masuk ke akun KoSeSurabaya Anda</p>
            </div>

            {{-- Form --}}
            <form method="POST" action="{{ route('login.submit') }}" class="space-y-5">
                @csrf

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-xs font-medium text-gray-400 mb-2">Email</label>
                    <div class="relative">
                        <i data-feather="mail" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500"></i>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                               class="w-full bg-white/5 border border-white/10 rounded-xl pl-10 pr-4 py-3 text-sm text-gray-200 placeholder-gray-500 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 focus:outline-none transition @error('email') border-red-500/50 @enderror"
                               placeholder="email@example.com">
                    </div>
                    @error('email')
                        <p class="text-xs text-red-400 mt-1.5 flex items-center gap-1">
                            <i data-feather="alert-circle" class="w-3 h-3"></i>{{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-xs font-medium text-gray-400 mb-2">Password</label>
                    <div class="relative" x-data="{ show: false }">
                        <i data-feather="lock" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500"></i>
                        <input :type="show ? 'text' : 'password'" name="password" id="password" required
                               class="w-full bg-white/5 border border-white/10 rounded-xl pl-10 pr-12 py-3 text-sm text-gray-200 placeholder-gray-500 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 focus:outline-none transition"
                               placeholder="••••••••">
                        <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-300 transition">
                            <i :data-feather="show ? 'eye-off' : 'eye'" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>

                {{-- Remember me --}}
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded bg-white/5 border-white/20 text-brand-500 focus:ring-brand-500 focus:ring-offset-0">
                        <span class="text-xs text-gray-400">Ingat saya</span>
                    </label>
                </div>

                {{-- Submit --}}
                <button type="submit"
                        class="w-full bg-gradient-to-r from-brand-500 to-brand-600 text-white py-3 rounded-xl font-semibold text-sm hover:shadow-lg hover:shadow-brand-500/25 transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                    <i data-feather="log-in" class="w-4 h-4"></i>
                    Masuk
                </button>
            </form>

            {{-- Register link --}}
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-400">
                    Belum punya akun?
                    <a href="{{ route('register') }}" class="text-brand-400 hover:text-brand-300 font-medium transition">Daftar Sekarang</a>
                </p>
            </div>
        </div>

        {{-- Trust badges --}}
        <div class="flex items-center justify-center gap-4 mt-6 text-xs text-gray-600">
            <span class="flex items-center gap-1">🔒 Terenkripsi</span>
            <span>•</span>
            <span class="flex items-center gap-1">✅ Aman</span>
            <span>•</span>
            <span class="flex items-center gap-1">🛡️ Terpercaya</span>
        </div>
    </div>
</div>
@endsection
