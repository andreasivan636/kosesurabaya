@extends('layouts.app')
@section('title', 'Verifikasi Kos - Admin - KoSeSurabaya')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-white">Verifikasi Kos</h1>
            <p class="text-sm text-gray-400 mt-0.5">{{ $kosPending->total() }} kos menunggu verifikasi</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-400 hover:text-white transition flex items-center gap-1">
            <i data-feather="arrow-left" class="w-4 h-4"></i> Dashboard
        </a>
    </div>

    @if($kosPending->isEmpty())
        <div class="glass rounded-2xl p-12 text-center">
            <span class="text-5xl">✅</span>
            <h3 class="text-lg font-semibold text-white mt-4">Semua Bersih!</h3>
            <p class="text-sm text-gray-400 mt-1">Tidak ada kos yang perlu diverifikasi saat ini.</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($kosPending as $kos)
                <div class="glass rounded-2xl p-6 hover:border-yellow-500/20 transition">
                    <div class="flex flex-col lg:flex-row gap-4">
                        <div class="flex-1">
                            <div class="flex items-start gap-3">
                                <div class="w-16 h-16 rounded-xl bg-white/5 flex-shrink-0 overflow-hidden">
                                    @if($kos->foto_utama)
                                        <img src="{{ asset('storage/' . $kos->foto_utama) }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-2xl">🏠</div>
                                    @endif
                                </div>
                                <div>
                                    <h3 class="font-semibold text-white">{{ $kos->nama_kos }}</h3>
                                    <p class="text-xs text-gray-400">{{ $kos->alamat }}</p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Pemilik: <span class="text-gray-300">{{ $kos->owner->name }}</span>
                                        ({{ $kos->owner->email }}) •
                                        📞 {{ $kos->owner->phone ?? '-' }} •
                                        Kos {{ ucfirst($kos->tipe_kos) }}
                                    </p>
                                </div>
                            </div>

                            {{-- Tipe Kamar Detail --}}
                            <div class="flex flex-wrap gap-2 mt-3">
                                @foreach($kos->tipeKamar as $tipe)
                                    <span class="px-2.5 py-1.5 rounded-lg bg-white/5 text-xs text-gray-300">
                                        Tipe {{ $tipe->tipe }}: Rp {{ number_format($tipe->harga_bulanan, 0, ',', '.') }}/bln •
                                        {{ $tipe->jumlah_kamar }} kamar •
                                        {{ implode(', ', $tipe->fasilitas ?? []) }}
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex lg:flex-col gap-2 flex-shrink-0">
                            <form method="POST" action="{{ route('admin.kos.approve', $kos->id) }}" class="flex-1 lg:flex-none">
                                @csrf
                                <button type="submit" class="w-full px-5 py-2.5 rounded-xl bg-brand-500/20 text-brand-400 text-sm font-medium hover:bg-brand-500/30 transition border border-brand-500/30">
                                    ✓ Approve
                                </button>
                            </form>
                            <button onclick="showRejectModal({{ $kos->id }}, '{{ addslashes($kos->nama_kos) }}')"
                                    class="flex-1 lg:flex-none px-5 py-2.5 rounded-xl bg-red-500/20 text-red-400 text-sm font-medium hover:bg-red-500/30 transition border border-red-500/30">
                                ✕ Tolak
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">{{ $kosPending->links() }}</div>
    @endif
</div>

{{-- Reject Modal (same as dashboard) --}}
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
