@extends('layouts.app')
@section('title', 'Daftar Akun - KoSeSurabaya')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">
        <div class="glass rounded-3xl p-8 shadow-2xl animate-slide-up">
            {{-- Header --}}
            <div class="text-center mb-6">
                <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-brand-400 to-accent-400 rounded-2xl flex items-center justify-center shadow-lg">
                    <span class="text-3xl">🏠</span>
                </div>
                <h1 class="text-2xl font-bold text-white">Buat Akun Baru</h1>
                <p class="text-sm text-gray-400 mt-1">Bergabung dengan KoSeSurabaya</p>
            </div>

            <form method="POST" action="{{ route('register.submit') }}" class="space-y-4">
                @csrf

                {{-- Nama --}}
                <div>
                    <label for="name" class="block text-xs font-medium text-gray-400 mb-1.5">Nama Lengkap</label>
                    <div class="relative">
                        <i data-feather="user" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500"></i>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus
                               class="w-full bg-white/5 border border-white/10 rounded-xl pl-10 pr-4 py-3 text-sm text-gray-200 placeholder-gray-500 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 focus:outline-none transition @error('name') border-red-500/50 @enderror"
                               placeholder="Nama lengkap Anda">
                    </div>
                    @error('name')
                        <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-xs font-medium text-gray-400 mb-1.5">Email</label>
                    <div class="relative">
                        <i data-feather="mail" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500"></i>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                               class="w-full bg-white/5 border border-white/10 rounded-xl pl-10 pr-4 py-3 text-sm text-gray-200 placeholder-gray-500 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 focus:outline-none transition @error('email') border-red-500/50 @enderror"
                               placeholder="email@example.com">
                    </div>
                    @error('email')
                        <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Phone --}}
                <div>
                    <label for="phone" class="block text-xs font-medium text-gray-400 mb-1.5">No. HP (opsional)</label>
                    <div class="relative">
                        <i data-feather="phone" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500"></i>
                        <input type="tel" name="phone" id="phone" value="{{ old('phone') }}"
                               class="w-full bg-white/5 border border-white/10 rounded-xl pl-10 pr-4 py-3 text-sm text-gray-200 placeholder-gray-500 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 focus:outline-none transition"
                               placeholder="08xxxxxxxxxx">
                    </div>
                </div>

                {{-- Role Selection --}}
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-2">Daftar Sebagai</label>
                    <div class="grid grid-cols-2 gap-3" x-data="{ role: '{{ old('role', 'customer') }}' }">
                        <label @click="role = 'customer'" :class="role === 'customer' ? 'border-brand-500 bg-brand-500/10' : 'border-white/10 bg-white/5'"
                               class="relative cursor-pointer rounded-xl border-2 p-4 text-center transition-all hover:bg-white/10">
                            <input type="radio" name="role" value="customer" class="hidden" :checked="role === 'customer'">
                            <span class="text-2xl block mb-1">🎓</span>
                            <span class="text-sm font-medium" :class="role === 'customer' ? 'text-brand-400' : 'text-gray-300'">Mahasiswa</span>
                            <p class="text-[10px] text-gray-500 mt-0.5">Cari & booking kos</p>
                        </label>
                        <label @click="role = 'owner'" :class="role === 'owner' ? 'border-brand-500 bg-brand-500/10' : 'border-white/10 bg-white/5'"
                               class="relative cursor-pointer rounded-xl border-2 p-4 text-center transition-all hover:bg-white/10">
                            <input type="radio" name="role" value="owner" class="hidden" :checked="role === 'owner'">
                            <span class="text-2xl block mb-1">🏘️</span>
                            <span class="text-sm font-medium" :class="role === 'owner' ? 'text-brand-400' : 'text-gray-300'">Pemilik Kos</span>
                            <p class="text-[10px] text-gray-500 mt-0.5">Daftarkan & kelola kos</p>
                        </label>
                    </div>
                    @error('role')
                        <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-xs font-medium text-gray-400 mb-1.5">Password</label>
                    <div class="relative">
                        <i data-feather="lock" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500"></i>
                        <input type="password" name="password" id="password" required
                               class="w-full bg-white/5 border border-white/10 rounded-xl pl-10 pr-4 py-3 text-sm text-gray-200 placeholder-gray-500 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 focus:outline-none transition @error('password') border-red-500/50 @enderror"
                               placeholder="Minimal 8 karakter">
                    </div>
                    @error('password')
                        <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Confirm Password --}}
                <div>
                    <label for="password_confirmation" class="block text-xs font-medium text-gray-400 mb-1.5">Konfirmasi Password</label>
                    <div class="relative">
                        <i data-feather="lock" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500"></i>
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                               class="w-full bg-white/5 border border-white/10 rounded-xl pl-10 pr-4 py-3 text-sm text-gray-200 placeholder-gray-500 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 focus:outline-none transition"
                               placeholder="Ulangi password">
                    </div>
                </div>

                {{-- Submit --}}
                <button type="submit"
                        class="w-full bg-gradient-to-r from-brand-500 to-brand-600 text-white py-3 rounded-xl font-semibold text-sm hover:shadow-lg hover:shadow-brand-500/25 transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                    <i data-feather="user-plus" class="w-4 h-4"></i>
                    Daftar Sekarang
                </button>
            </form>

            <div class="mt-5 text-center">
                <p class="text-sm text-gray-400">
                    Sudah punya akun?
                    <a href="{{ route('login') }}" class="text-brand-400 hover:text-brand-300 font-medium transition">Masuk</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
