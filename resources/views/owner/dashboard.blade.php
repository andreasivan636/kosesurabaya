@extends('layouts.app')
@section('title', 'Dashboard Pemilik Kos - KoSeSurabaya')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">Dashboard Pemilik</h1>
            <p class="text-sm text-gray-400 mt-1">Kelola properti kos Anda dengan mudah</p>
        </div>
        <a href="{{ route('owner.kos.create.step1') }}"
           class="inline-flex items-center gap-2 px-5 py-3 bg-gradient-to-r from-brand-500 to-brand-600 text-white rounded-xl font-semibold text-sm hover:shadow-lg hover:shadow-brand-500/25 transition-all active:scale-95">
            <i data-feather="plus" class="w-4 h-4"></i>
            Daftarkan Kos Baru
        </a>
    </div>

    {{-- Alert Sukses (Hijau) --}}
    @if(session('success'))
        <div class="mb-6 p-4 rounded-2xl bg-brand-500/10 border border-brand-500/30 text-brand-300 flex items-start gap-3 shadow-lg shadow-brand-500/5">
            <div class="w-8 h-8 rounded-xl bg-brand-500/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                <i data-feather="check-circle" class="w-5 h-5 text-brand-400"></i>
            </div>
            <div class="flex-1">
                <h4 class="text-sm font-bold text-white mb-0.5">Berhasil!</h4>
                <p class="text-sm text-brand-200">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    {{-- Alert Error (Merah) --}}
    @if(session('error'))
        <div class="mb-6 p-4 rounded-2xl bg-red-500/10 border border-red-500/30 text-red-300 flex items-start gap-3 shadow-lg shadow-red-500/5">
            <div class="w-8 h-8 rounded-xl bg-red-500/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                <i data-feather="alert-circle" class="w-5 h-5 text-red-400"></i>
            </div>
            <div class="flex-1">
                <h4 class="text-sm font-bold text-white mb-0.5">Perhatian</h4>
                <p class="text-sm text-red-200">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    {{-- Statistik Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        @php
            $statItems = [
                ['label' => 'Total Kos', 'value' => $stats['total_kos'], 'icon' => 'home', 'color' => 'brand'],
                ['label' => 'Total Kamar', 'value' => $stats['total_kamar'], 'icon' => 'grid', 'color' => 'blue'],
                ['label' => 'Kamar Terisi', 'value' => $stats['kamar_terisi'], 'icon' => 'check-circle', 'color' => 'green'],
                ['label' => 'Total Booking', 'value' => $stats['total_booking'], 'icon' => 'calendar', 'color' => 'accent'],
            ];
        @endphp

        @foreach($statItems as $stat)
            <div class="glass rounded-2xl p-5 hover:border-brand-500/20 transition-colors">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 rounded-xl bg-{{ $stat['color'] === 'brand' ? 'brand' : ($stat['color'] === 'accent' ? 'accent' : $stat['color']) }}-500/20 flex items-center justify-center">
                        <i data-feather="{{ $stat['icon'] }}" class="w-5 h-5 text-{{ $stat['color'] === 'accent' ? 'accent-400' : ($stat['color'] === 'brand' ? 'brand-400' : $stat['color'] . '-400') }}"></i>
                    </div>
                </div>
                <div class="text-2xl font-bold text-white">{{ number_format($stat['value']) }}</div>
                <div class="text-xs text-gray-500 mt-0.5">{{ $stat['label'] }}</div>
            </div>
        @endforeach
    </div>

    {{-- Daftar Kos --}}
    <h2 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
        <i data-feather="list" class="w-5 h-5 text-brand-400"></i>
        Kos Anda
    </h2>

    @if($kosList->isEmpty())
        <div class="glass rounded-2xl p-12 text-center">
            <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-white/5 flex items-center justify-center">
                <span class="text-4xl">🏠</span>
            </div>
            <h3 class="text-lg font-semibold text-white mb-2">Belum Ada Kos Terdaftar</h3>
            <p class="text-sm text-gray-400 mb-4">Mulai daftarkan properti kos Anda untuk menjangkau ribuan mahasiswa di Surabaya.</p>
            <a href="{{ route('owner.kos.create.step1') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-brand-500 to-brand-600 text-white rounded-xl font-semibold text-sm hover:shadow-lg hover:shadow-brand-500/25 transition-all">
                <i data-feather="plus" class="w-4 h-4"></i> Daftarkan Kos Pertama
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($kosList as $kos)
                <div class="glass rounded-2xl p-5 hover:border-brand-500/20 transition-colors">
                    <div class="flex flex-col sm:flex-row gap-4">
                        {{-- Foto --}}
                        <div class="w-full sm:w-32 h-32 sm:h-24 rounded-xl overflow-hidden bg-white/5 flex-shrink-0">
                            @if($kos->foto_utama)
                                <img src="{{ asset('storage/' . $kos->foto_utama) }}" alt="{{ $kos->nama_kos }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-3xl">🏠</div>
                            @endif
                        </div>

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <h3 class="font-semibold text-white">{{ $kos->nama_kos }}</h3>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $kos->alamat }}</p>
                                </div>

                                {{-- Status Badge & Tombol Premium --}}
                                <div class="flex items-center gap-2 flex-wrap justify-end">
                                    @php
                                        $statusColors = [
                                            'pending' => 'bg-yellow-500/20 text-yellow-400 border-yellow-500/30',
                                            'approved' => 'bg-brand-500/20 text-brand-400 border-brand-500/30',
                                            'rejected' => 'bg-red-500/20 text-red-400 border-red-500/30',
                                        ];
                                        $statusLabels = [
                                            'pending' => '⏳ Menunggu Verifikasi',
                                            'approved' => '✅ Disetujui',
                                            'rejected' => '❌ Ditolak',
                                        ];
                                    @endphp
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-medium border flex-shrink-0 {{ $statusColors[$kos->status] }}">
                                        {{ $statusLabels[$kos->status] }}
                                    </span>

                                    @if($kos->status === 'approved')
                                        @if($kos->isPremium())
                                            <span class="px-3 py-1 rounded-lg text-xs font-bold bg-amber-500/20 text-amber-300 border border-amber-500/40 flex items-center gap-1 shadow-sm shadow-amber-500/20">
                                                💎 Status: Premium
                                            </span>
                                        @else
                                            <button type="button"
                                                    onclick="upgradeKosToPremium({{ $kos->id }}, '{{ addslashes($kos->nama_kos) }}')"
                                                    id="btn-premium-{{ $kos->id }}"
                                                    class="inline-flex items-center gap-1 px-3 py-1 bg-gradient-to-r from-amber-500 to-yellow-500 hover:from-amber-400 hover:to-yellow-400 text-dark-900 font-bold text-xs rounded-lg shadow-md hover:shadow-amber-500/25 transition-all active:scale-95 cursor-pointer">
                                                <span>🌟</span> Jadikan Premium
                                            </button>
                                        @endif
                                    @endif
                                </div>
                            </div>

                            {{-- Tipe kamar summary --}}
                            <div class="flex flex-wrap gap-2 mt-3">
                                @foreach($kos->tipeKamar as $tipe)
                                    <div class="px-2.5 py-1.5 rounded-lg bg-white/5 text-xs text-gray-300">
                                        <span class="font-semibold text-brand-400">Tipe {{ $tipe->tipe }}</span> •
                                        Rp {{ number_format($tipe->harga_bulanan, 0, ',', '.') }}/bln •
                                        <span class="{{ $tipe->kamar_tersedia > 0 ? 'text-brand-400' : 'text-red-400' }}">
                                            {{ $tipe->kamar_tersedia }}/{{ $tipe->jumlah_kamar }} kosong
                                        </span>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Rejected reason --}}
                            @if($kos->status === 'rejected' && $kos->alasan_reject)
                                <div class="mt-2 px-3 py-2 rounded-lg bg-red-500/10 border border-red-500/20">
                                    <p class="text-xs text-red-400"><strong>Alasan:</strong> {{ $kos->alasan_reject }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection

@section('scripts')
{{-- Midtrans Snap.js (Sandbox) --}}
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key', env('MIDTRANS_CLIENT_KEY')) }}"></script>

<script>
async function upgradeKosToPremium(kosId, kosNama) {
    const btn = document.getElementById(`btn-premium-${kosId}`);
    const originalText = btn ? btn.innerHTML : '';

    if (btn) {
        btn.disabled = true;
        btn.innerHTML = `<span class="animate-spin inline-block mr-1">⏳</span> Menyiapkan...`;
    }

    try {
        const response = await fetch(`/owner/kos/${kosId}/subscribe`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': window.CSRF_TOKEN,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
        });

        const data = await response.json();
        console.log('Subscribe Premium Response:', data);

        if (response.ok && data.success && data.snap_token) {
            // Buka popup Midtrans Snap
            window.snap.pay(data.snap_token, {
                onSuccess: async function(result) {
                    console.log('Payment success:', result);
                    if (btn) {
                        btn.innerHTML = `<span class="animate-spin inline-block mr-1">⚡</span> Mengaktifkan...`;
                    }

                    // Request AJAX ke backend untuk aktivasi is_highlighted
                    try {
                        const activateRes = await fetch(`/owner/kos/${kosId}/activate-premium`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': window.CSRF_TOKEN,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                order_id: data.order_id,
                                midtrans_result: result
                            }),
                        });

                        const activateData = await activateRes.json();
                        if (activateRes.ok && activateData.success) {
                            alert(activateData.message || 'Berhasil berlangganan Premium! Kos Anda kini menyala di peta.');
                            window.location.reload();
                        } else {
                            alert('Gagal mengaktifkan status premium: ' + (activateData.message || activateData.error_detail || 'Terjadi kesalahan'));
                            if (btn) {
                                btn.disabled = false;
                                btn.innerHTML = originalText;
                            }
                        }
                    } catch (actErr) {
                        console.error('Activation AJAX error:', actErr);
                        alert('Terjadi kesalahan saat mengaktifkan premium. Silakan muat ulang halaman.');
                        window.location.reload();
                    }
                },
                onPending: function(result) {
                    console.log('Payment pending:', result);
                    alert('Pembayaran Anda sedang diproses. Silakan selesaikan pembayaran.');
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = originalText;
                    }
                },
                onError: function(result) {
                    console.error('Payment error:', result);
                    alert('Pembayaran gagal: ' + (result?.status_message || JSON.stringify(result)));
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = originalText;
                    }
                },
                onClose: function() {
                    console.log('Snap popup closed');
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = originalText;
                    }
                }
            });
        } else {
            let errorMsg = data.message || data.error_detail;
            if (!errorMsg && data.errors) {
                errorMsg = Object.values(data.errors).flat().join(', ');
            }
            alert('Gagal memproses langganan premium: ' + (errorMsg || 'Terjadi kesalahan sistem.'));
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        }
    } catch (err) {
        console.error('Fetch error:', err);
        alert('Gagal terhubung ke server: ' + err.message);
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    }
}
</script>
@endsection
