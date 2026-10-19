@extends('layouts.app')
@section('title', 'Admin Dashboard - KoSeSurabaya')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-white flex items-center gap-2">
            <i data-feather="shield" class="w-6 h-6 text-brand-400"></i>
            Admin Dashboard
        </h1>
        <p class="text-sm text-gray-400 mt-1">Kelola platform KoSeSurabaya</p>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        @php
            $adminStats = [
                ['label' => 'Total Pengguna', 'value' => $stats['total_users'], 'icon' => 'users', 'gradient' => 'from-blue-500 to-blue-600'],
                ['label' => 'Kos Pending', 'value' => $stats['kos_pending'], 'icon' => 'clock', 'gradient' => 'from-yellow-500 to-orange-500'],
                ['label' => 'Kos Approved', 'value' => $stats['kos_approved'], 'icon' => 'check-circle', 'gradient' => 'from-brand-500 to-brand-600'],
                ['label' => 'Pendapatan', 'value' => 'Rp ' . number_format($stats['total_pendapatan'] / 1000000, 1) . ' jt', 'icon' => 'dollar-sign', 'gradient' => 'from-purple-500 to-pink-500'],
            ];
        @endphp

        @foreach($adminStats as $stat)
            <div class="glass rounded-2xl p-5 hover:border-brand-500/20 transition group">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-r {{ $stat['gradient'] }} flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                    <i data-feather="{{ $stat['icon'] }}" class="w-5 h-5 text-white"></i>
                </div>
                <div class="text-xl font-bold text-white">{{ is_numeric($stat['value']) ? number_format($stat['value']) : $stat['value'] }}</div>
                <div class="text-xs text-gray-500 mt-0.5">{{ $stat['label'] }}</div>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Kos Pending Verification --}}
        <div class="glass rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-semibold text-white flex items-center gap-2">
                    <i data-feather="clock" class="w-4 h-4 text-yellow-400"></i>
                    Menunggu Verifikasi
                </h2>
                <a href="{{ route('admin.kos.pending') }}" class="text-xs text-brand-400 hover:text-brand-300 transition">Lihat Semua →</a>
            </div>

            @if($kosPending->isEmpty())
                <div class="text-center py-8">
                    <span class="text-3xl">✅</span>
                    <p class="text-sm text-gray-400 mt-2">Tidak ada kos yang menunggu verifikasi</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($kosPending as $kos)
                        <div class="bg-white/5 rounded-xl p-4 hover:bg-white/10 transition">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0 flex-1">
                                    <h4 class="text-sm font-semibold text-white truncate">{{ $kos->nama_kos }}</h4>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $kos->alamat }}</p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Oleh: <span class="text-gray-300">{{ $kos->owner->name }}</span> •
                                        {{ $kos->created_at->diffForHumans() }}
                                    </p>
                                </div>
                                <div class="flex gap-1.5 flex-shrink-0">
                                    <form method="POST" action="{{ route('admin.kos.approve', $kos->id) }}">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-brand-500/20 text-brand-400 text-xs font-medium hover:bg-brand-500/30 transition" title="Approve">
                                            ✓ Approve
                                        </button>
                                    </form>
                                    <button onclick="showRejectModal({{ $kos->id }}, '{{ $kos->nama_kos }}')" class="px-3 py-1.5 rounded-lg bg-red-500/20 text-red-400 text-xs font-medium hover:bg-red-500/30 transition" title="Reject">
                                            ✕ Tolak
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Transaksi Terbaru --}}
        <div class="glass rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-semibold text-white flex items-center gap-2">
                    <i data-feather="credit-card" class="w-4 h-4 text-purple-400"></i>
                    Transaksi Terbaru
                </h2>
                <a href="{{ route('admin.transaksi') }}" class="text-xs text-brand-400 hover:text-brand-300 transition">Lihat Semua →</a>
            </div>

            @if($transaksiTerbaru->isEmpty())
                <div class="text-center py-8">
                    <span class="text-3xl">💳</span>
                    <p class="text-sm text-gray-400 mt-2">Belum ada transaksi</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($transaksiTerbaru as $trx)
                        <div class="bg-white/5 rounded-xl p-3 flex items-center justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <p class="text-sm text-white truncate">{{ $trx->kos->nama_kos ?? '-' }}</p>
                                <p class="text-xs text-gray-400">{{ $trx->user->name ?? '-' }} • {{ $trx->created_at->diffForHumans() }}</p>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <div class="text-sm font-bold text-brand-400">Rp {{ number_format($trx->jumlah_bayar, 0, ',', '.') }}</div>
                                @php
                                    $trxColors = [
                                        'paid' => 'text-brand-400', 'pending' => 'text-yellow-400',
                                        'waiting_payment' => 'text-yellow-400', 'expired' => 'text-gray-500',
                                        'cancelled' => 'text-red-400',
                                    ];
                                @endphp
                                <span class="text-xs {{ $trxColors[$trx->status] ?? 'text-gray-400' }}">{{ ucfirst($trx->status) }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
        <a href="{{ route('admin.kos.pending') }}" class="glass rounded-2xl p-5 text-center hover:border-brand-500/20 transition group">
            <i data-feather="clipboard" class="w-6 h-6 text-yellow-400 mx-auto mb-2 group-hover:scale-110 transition-transform"></i>
            <span class="text-sm text-gray-300">Verifikasi Kos</span>
        </a>
        <a href="{{ route('admin.transaksi') }}" class="glass rounded-2xl p-5 text-center hover:border-brand-500/20 transition group">
            <i data-feather="credit-card" class="w-6 h-6 text-purple-400 mx-auto mb-2 group-hover:scale-110 transition-transform"></i>
            <span class="text-sm text-gray-300">Transaksi</span>
        </a>
        <a href="{{ route('admin.users') }}" class="glass rounded-2xl p-5 text-center hover:border-brand-500/20 transition group">
            <i data-feather="users" class="w-6 h-6 text-blue-400 mx-auto mb-2 group-hover:scale-110 transition-transform"></i>
            <span class="text-sm text-gray-300">Pengguna</span>
        </a>
        <a href="{{ route('home') }}" class="glass rounded-2xl p-5 text-center hover:border-brand-500/20 transition group">
            <i data-feather="map" class="w-6 h-6 text-brand-400 mx-auto mb-2 group-hover:scale-110 transition-transform"></i>
            <span class="text-sm text-gray-300">Lihat Peta</span>
        </a>
    </div>
</div>

{{-- Reject Modal --}}
<div id="reject-modal" class="fixed inset-0 z-[60] hidden">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeRejectModal()"></div>
    <div class="absolute bottom-0 left-0 right-0 sm:bottom-auto sm:top-1/2 sm:left-1/2 sm:-translate-x-1/2 sm:-translate-y-1/2 sm:max-w-md sm:w-full glass rounded-t-3xl sm:rounded-3xl p-6 animate-slide-up">
        <h3 class="text-lg font-bold text-white mb-1">Tolak Kos</h3>
        <p class="text-sm text-gray-400 mb-4" id="reject-kos-name"></p>
        <form method="POST" id="reject-form">
            @csrf
            <textarea name="alasan_reject" rows="3" required placeholder="Berikan alasan penolakan..."
                      class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-sm text-gray-200 focus:border-red-500 focus:outline-none transition resize-none mb-4"></textarea>
            <div class="flex gap-3">
                <button type="button" onclick="closeRejectModal()" class="flex-1 px-4 py-2.5 rounded-xl bg-white/5 text-gray-400 text-sm border border-white/10">Batal</button>
                <button type="submit" class="flex-1 px-4 py-2.5 rounded-xl bg-red-500/20 text-red-400 text-sm font-medium border border-red-500/30 hover:bg-red-500/30 transition">Tolak Kos</button>
            </div>
        </form>
    </div>
</div>

<script>
function showRejectModal(id, name) {
    document.getElementById('reject-form').action = `/admin/kos/${id}/reject`;
    document.getElementById('reject-kos-name').textContent = `Kos: ${name}`;
    document.getElementById('reject-modal').classList.remove('hidden');
}
function closeRejectModal() {
    document.getElementById('reject-modal').classList.add('hidden');
}
</script>
@endsection
