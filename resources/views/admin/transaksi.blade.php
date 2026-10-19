@extends('layouts.app')
@section('title', 'Transaksi - Admin - KoSeSurabaya')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-white">Manajemen Transaksi</h1>
            <p class="text-sm text-gray-400 mt-0.5">Pantau semua pembayaran masuk</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-400 hover:text-white transition flex items-center gap-1">
            <i data-feather="arrow-left" class="w-4 h-4"></i> Dashboard
        </a>
    </div>

    {{-- Status Filter --}}
    <div class="flex flex-wrap gap-2 mb-6">
        @foreach(['all' => 'Semua', 'paid' => '✅ Paid', 'pending' => '⏳ Pending', 'waiting_payment' => '💳 Waiting', 'expired' => '⌛ Expired', 'cancelled' => '❌ Cancelled'] as $key => $label)
            <a href="{{ route('admin.transaksi', ['status' => $key === 'all' ? null : $key]) }}"
               class="px-3 py-1.5 rounded-lg text-xs font-medium transition {{ request('status') === $key || (!request('status') && $key === 'all') ? 'bg-brand-500/20 text-brand-400 border border-brand-500/30' : 'bg-white/5 text-gray-400 border border-white/10 hover:bg-white/10' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    {{-- Table --}}
    <div class="glass rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-white/5">
                        <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Order ID</th>
                        <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Customer</th>
                        <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Kos</th>
                        <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Tipe</th>
                        <th class="text-right px-4 py-3 text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                        <th class="text-center px-4 py-3 text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="text-right px-4 py-3 text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($transaksi as $trx)
                        <tr class="hover:bg-white/5 transition">
                            <td class="px-4 py-3 font-mono text-xs text-gray-400">{{ $trx->order_id }}</td>
                            <td class="px-4 py-3">
                                <div class="text-white text-xs">{{ $trx->user->name ?? '-' }}</div>
                                <div class="text-gray-500 text-[10px]">{{ $trx->user->email ?? '' }}</div>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-300 max-w-[150px] truncate">{{ $trx->kos->nama_kos ?? '-' }}</td>
                            <td class="px-4 py-3 text-xs text-gray-400">{{ $trx->tipeKamar->nama_tipe ?? '-' }}</td>
                            <td class="px-4 py-3 text-right text-xs font-semibold text-brand-400">Rp {{ number_format($trx->jumlah_bayar, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-center">
                                @php
                                    $colors = ['paid' => 'bg-brand-500/20 text-brand-400', 'pending' => 'bg-yellow-500/20 text-yellow-400', 'waiting_payment' => 'bg-blue-500/20 text-blue-400', 'expired' => 'bg-gray-500/20 text-gray-400', 'cancelled' => 'bg-red-500/20 text-red-400', 'refunded' => 'bg-purple-500/20 text-purple-400'];
                                @endphp
                                <span class="px-2 py-1 rounded-lg text-[10px] font-medium {{ $colors[$trx->status] ?? 'bg-white/10 text-gray-400' }}">{{ ucfirst($trx->status) }}</span>
                            </td>
                            <td class="px-4 py-3 text-right text-xs text-gray-500">{{ $trx->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-12 text-center text-gray-500">Tidak ada transaksi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">{{ $transaksi->appends(request()->query())->links() }}</div>
</div>
@endsection
