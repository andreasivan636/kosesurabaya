@extends('layouts.app')
@section('title', 'Daftarkan Kos - Langkah 2 - KoSeSurabaya')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-8">
    {{-- Progress Steps --}}
    <div class="flex items-center justify-center gap-2 mb-8">
        @foreach([['1', 'Info Dasar', false], ['2', 'Foto & Fasilitas', true], ['3', 'Tipe Kamar', false]] as [$num, $label, $active])
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold {{ $loop->index < 1 ? 'bg-brand-500/30 text-brand-400' : ($active ? 'bg-brand-500 text-white' : 'bg-white/10 text-gray-500') }}">{{ $num }}</div>
                <span class="text-xs {{ $active ? 'text-white' : 'text-gray-500' }} hidden sm:inline">{{ $label }}</span>
            </div>
            @if(!$loop->last)
                <div class="w-8 h-px {{ $loop->index < 1 ? 'bg-brand-500' : 'bg-white/10' }}"></div>
            @endif
        @endforeach
    </div>

    <div class="glass rounded-3xl p-6 md:p-8 shadow-2xl">
        <h1 class="text-xl font-bold text-white mb-1">📸 Foto & Fasilitas Umum</h1>
        <p class="text-sm text-gray-400 mb-6">Langkah 2 dari 3 — Upload foto dan pilih fasilitas</p>

        <form method="POST" action="{{ route('owner.kos.store.step2') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf

            {{-- Foto Utama --}}
            <div>
                <label class="block text-xs font-medium text-gray-400 mb-1.5">Foto Utama * (tampil di peta & pencarian)</label>
                <div class="relative" x-data="{ preview: null }">
                    <input type="file" name="foto_utama" accept="image/*" required
                           @change="preview = URL.createObjectURL($event.target.files[0])"
                           class="w-full bg-white/5 border border-white/10 border-dashed rounded-xl px-4 py-8 text-sm text-gray-400 focus:border-brand-500 focus:outline-none transition cursor-pointer file:mr-3 file:rounded-lg file:border-0 file:bg-brand-500/20 file:text-brand-400 file:px-3 file:py-1.5 file:text-xs file:font-medium file:cursor-pointer">
                    <img x-show="preview" :src="preview" class="mt-2 w-full h-40 object-cover rounded-xl" alt="Preview">
                </div>
                @error('foto_utama') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Foto Tambahan --}}
            <div>
                <label class="block text-xs font-medium text-gray-400 mb-1.5">Foto Tambahan (opsional, max 5)</label>
                <input type="file" name="foto[]" accept="image/*" multiple
                       class="w-full bg-white/5 border border-white/10 border-dashed rounded-xl px-4 py-6 text-sm text-gray-400 focus:border-brand-500 focus:outline-none transition cursor-pointer file:mr-3 file:rounded-lg file:border-0 file:bg-white/10 file:text-gray-300 file:px-3 file:py-1.5 file:text-xs file:font-medium file:cursor-pointer">
            </div>

            {{-- Fasilitas Umum --}}
            <div>
                <label class="block text-xs font-medium text-gray-400 mb-3">🏢 Fasilitas Umum (area bersama)</label>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                    @php
                        $fasilitasUmumOptions = [
                            'Parkir Motor', 'Parkir Mobil', 'Dapur Bersama', 'Ruang Tamu',
                            'Jemuran', 'CCTV', 'Penjaga Kos', 'Laundry',
                            'WiFi Area', 'Musholla', 'Taman', 'Rooftop',
                        ];
                    @endphp
                    @foreach($fasilitasUmumOptions as $fas)
                        <label class="flex items-center gap-2 px-3 py-2.5 rounded-xl bg-white/5 border border-white/10 cursor-pointer hover:bg-white/10 transition has-[:checked]:bg-brand-500/10 has-[:checked]:border-brand-500/30">
                            <input type="checkbox" name="fasilitas_umum[]" value="{{ $fas }}"
                                   class="w-4 h-4 rounded bg-white/10 border-white/20 text-brand-500 focus:ring-brand-500 focus:ring-offset-0">
                            <span class="text-xs text-gray-300">{{ $fas }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Buttons --}}
            <div class="flex gap-3">
                <a href="{{ route('owner.kos.create.step1') }}"
                   class="flex-1 text-center px-4 py-3 rounded-xl bg-white/5 text-gray-400 text-sm font-medium hover:bg-white/10 transition border border-white/10">
                    ← Kembali
                </a>
                <button type="submit"
                        class="flex-1 bg-gradient-to-r from-brand-500 to-brand-600 text-white py-3 rounded-xl font-semibold text-sm hover:shadow-lg hover:shadow-brand-500/25 transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                    Lanjut ke Tipe Kamar
                    <i data-feather="arrow-right" class="w-4 h-4"></i>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
