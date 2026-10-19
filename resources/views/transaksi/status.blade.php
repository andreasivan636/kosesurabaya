@extends('layouts.app')
@section('title', 'Status Pembayaran - KoSeSurabaya')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">
        <div class="glass rounded-3xl p-8 shadow-2xl text-center animate-slide-up">

            @if($transaksi->status === 'paid')
                {{-- SUCCESS --}}
                <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-brand-500/20 flex items-center justify-center">
                    <span class="text-5xl">✅</span>
                </div>
                <h1 class="text-2xl font-bold text-white mb-2">Pembayaran Berhasil!</h1>
                <p class="text-sm text-gray-400 mb-6">Booking kos Anda telah dikonfirmasi.</p>

            @elseif(in_array($transaksi->status, ['pending', 'waiting_payment']))
                {{-- PENDING --}}
                <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-yellow-500/20 flex items-center justify-center">
                    <span class="text-5xl animate-pulse">⏳</span>
                </div>
                <h1 class="text-2xl font-bold text-white mb-2">Menunggu Pembayaran</h1>
                <p class="text-sm text-gray-400 mb-6">Silakan selesaikan pembayaran Anda.</p>

                @if($transaksi->snap_token)
                    <button onclick="window.snap.pay('{{ $transaksi->snap_token }}')"
                            class="w-full bg-gradient-to-r from-brand-500 to-brand-600 text-white py-3 rounded-xl font-semibold text-sm hover:shadow-lg hover:shadow-brand-500/25 transition-all active:scale-95 mb-4">
                        💳 Bayar Sekarang
                    </button>
                @endif

            @elseif($transaksi->status === 'expired')
                <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-gray-500/20 flex items-center justify-center">
                    <span class="text-5xl">⌛</span>
                </div>
                <h1 class="text-2xl font-bold text-white mb-2">Pembayaran Kedaluwarsa</h1>
                <p class="text-sm text-gray-400 mb-6">Batas waktu pembayaran telah habis.</p>

            @else
                <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-red-500/20 flex items-center justify-center">
                    <span class="text-5xl">❌</span>
                </div>
                <h1 class="text-2xl font-bold text-white mb-2">Pembayaran Dibatalkan</h1>
                <p class="text-sm text-gray-400 mb-6">Transaksi ini telah dibatalkan.</p>
            @endif

            {{-- Detail Transaksi --}}
            <div class="glass rounded-2xl p-5 text-left space-y-3 mb-6">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-400">Order ID</span>
                    <span class="text-gray-200 font-mono text-xs">{{ $transaksi->order_id }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-400">Kos</span>
                    <span class="text-white font-medium">{{ $transaksi->kos->nama_kos ?? '-' }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-400">Tipe Kamar</span>
                    <span class="text-gray-200">{{ $transaksi->tipeKamar->nama_tipe ?? '-' }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-400">Durasi</span>
                    <span class="text-gray-200">{{ ucfirst($transaksi->durasi) }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-400">Periode</span>
                    <span class="text-gray-200">{{ $transaksi->tanggal_mulai->format('d/m/Y') }} — {{ $transaksi->tanggal_selesai->format('d/m/Y') }}</span>
                </div>
                <div class="border-t border-white/5 pt-3 flex justify-between">
                    <span class="text-gray-400 font-medium">Total</span>
                    <span class="text-brand-400 font-bold text-lg">Rp {{ number_format($transaksi->jumlah_bayar, 0, ',', '.') }}</span>
                </div>
                @if($transaksi->payment_type)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">Metode</span>
                        <span class="text-gray-200">{{ ucfirst(str_replace('_', ' ', $transaksi->payment_type)) }}</span>
                    </div>
                @endif
                @if($transaksi->paid_at)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">Dibayar</span>
                        <span class="text-brand-400">{{ $transaksi->paid_at->format('d/m/Y H:i') }}</span>
                    </div>
                @endif
            </div>

            @if($transaksi->status === 'paid')
                <div class="bg-brand-500/10 border border-brand-500/20 rounded-xl p-4 mb-4">
                    <p class="text-xs text-brand-300">
                        🎉 <strong>E-Tiket Booking</strong> — Tunjukkan halaman ini kepada pemilik kos sebagai bukti booking resmi Anda.
                    </p>
                </div>
            @endif

            <a href="{{ route('home') }}"
               class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-white transition">
                <i data-feather="arrow-left" class="w-4 h-4"></i>
                Kembali ke Peta
            </a>
        </div>
    </div>
</div>

<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
@endsection
