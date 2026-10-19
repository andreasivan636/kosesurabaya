@extends('layouts.app')

@section('title', $kos->nama_kos . ' - KoSeSurabaya')

@section('styles')
<style>
    .foto-gallery { scroll-snap-type: x mandatory; -webkit-overflow-scrolling: touch; }
    .foto-gallery img { scroll-snap-align: start; }
</style>
@endsection

@section('content')
<div class="pb-28"> {{-- Padding untuk sticky footer --}}

    {{-- Hero: Galeri Foto --}}
    <div class="relative">
        <div class="foto-gallery flex overflow-x-auto gap-1 bg-dark-800">
            @if($kos->foto_utama)
                <img src="{{ asset('storage/' . $kos->foto_utama) }}"
                     alt="{{ $kos->nama_kos }}"
                     class="w-full md:w-2/3 h-64 md:h-96 object-cover flex-shrink-0">
            @endif
            @if($kos->foto && is_array($kos->foto))
                @foreach($kos->foto as $foto)
                    <img src="{{ asset('storage/' . $foto) }}"
                         alt="Foto {{ $kos->nama_kos }}"
                         class="w-3/4 md:w-1/3 h-64 md:h-96 object-cover flex-shrink-0">
                @endforeach
            @endif
        </div>

        {{-- Back button overlay --}}
        <a href="{{ route('home') }}" class="absolute top-4 left-4 glass w-10 h-10 rounded-full flex items-center justify-center hover:bg-white/20 transition">
            <i data-feather="arrow-left" class="w-5 h-5"></i>
        </a>
    </div>

    <div class="max-w-4xl mx-auto px-4 py-6 space-y-6">

        {{-- Header: Nama, Badge, Rating --}}
        <div class="animate-slide-up">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <h1 class="text-2xl md:text-3xl font-bold text-white">{{ $kos->nama_kos }}</h1>
                        @if($isVerified)
                            <span class="inline-flex items-center gap-1 bg-brand-500/20 text-brand-400 px-2.5 py-1 rounded-lg text-xs font-semibold border border-brand-500/30">
                                <i data-feather="check-circle" class="w-3.5 h-3.5"></i>
                                Terverifikasi
                            </span>
                        @endif
                    </div>
                    <p class="text-gray-400 text-sm flex items-center gap-1.5">
                        <i data-feather="map-pin" class="w-4 h-4 text-brand-400"></i>
                        {{ $kos->alamat }}
                    </p>
                </div>

                {{-- Rating --}}
                <div class="glass rounded-xl px-4 py-3 text-center">
                    <div class="text-2xl font-bold text-accent-400">{{ number_format($kos->rating_avg, 1) }}</div>
                    <div class="flex text-accent-400 text-xs mt-0.5">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= round($kos->rating_avg))
                                <span>★</span>
                            @else
                                <span class="text-gray-600">★</span>
                            @endif
                        @endfor
                    </div>
                    <div class="text-xs text-gray-500 mt-1">{{ $kos->rating_count }} ulasan</div>
                </div>
            </div>

            {{-- Tags --}}
            <div class="flex flex-wrap gap-2 mt-3">
                <span class="px-3 py-1 rounded-full text-xs font-medium
                    {{ $kos->tipe_kos === 'putra' ? 'bg-blue-500/20 text-blue-400' : ($kos->tipe_kos === 'putri' ? 'bg-pink-500/20 text-pink-400' : 'bg-purple-500/20 text-purple-400') }}">
                    🏠 Kos {{ ucfirst($kos->tipe_kos) }}
                </span>
                @if($kos->kampus_terdekat)
                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-accent-500/20 text-accent-400">
                        🎓 Dekat {{ $kos->kampus_terdekat }}
                    </span>
                @endif
                @if($kos->jarak_kampus_km)
                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-white/5 text-gray-400">
                        📍 {{ $kos->jarak_kampus_km }} km
                    </span>
                @endif
                <span class="px-3 py-1 rounded-full text-xs font-medium bg-brand-500/20 text-brand-400">
                    🛏️ {{ $kos->total_kamar_kosong }} kamar kosong
                </span>
            </div>
        </div>

        {{-- Deskripsi --}}
        @if($kos->deskripsi)
            <div class="glass rounded-2xl p-5 animate-slide-up">
                <h2 class="text-sm font-semibold text-gray-300 mb-2 flex items-center gap-2">
                    <i data-feather="file-text" class="w-4 h-4 text-brand-400"></i>
                    Deskripsi
                </h2>
                <p class="text-gray-400 text-sm leading-relaxed">{{ $kos->deskripsi }}</p>
            </div>
        @endif

        {{-- Fasilitas Umum --}}
        @if($kos->fasilitas_umum && count($kos->fasilitas_umum) > 0)
            <div class="glass rounded-2xl p-5 animate-slide-up">
                <h2 class="text-sm font-semibold text-gray-300 mb-3 flex items-center gap-2">
                    <i data-feather="check-square" class="w-4 h-4 text-brand-400"></i>
                    Fasilitas Umum
                </h2>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                    @foreach($kos->fasilitas_umum as $fasilitas)
                        <div class="flex items-center gap-2 bg-white/5 rounded-xl px-3 py-2.5">
                            @php
                                $iconName = \App\Models\TipeKamar::FASILITAS_ICONS[$fasilitas] ?? 'check';
                            @endphp
                            <i data-feather="{{ $iconName }}" class="w-4 h-4 text-brand-400 flex-shrink-0"></i>
                            <span class="text-xs text-gray-300">{{ $fasilitas }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Tipe Kamar (Dinamis Sesuai Database) --}}
        <div>
            <h2 class="text-sm font-semibold text-gray-300 mb-3 flex items-center gap-2">
                <i data-feather="layers" class="w-4 h-4 text-brand-400"></i>
                Tipe Kamar ({{ $kos->tipeKamar->count() }} Pilihan)
            </h2>
            <div class="space-y-4">
                @forelse($kos->tipeKamar as $tipe)
                    <div class="glass rounded-2xl p-5 hover:border-brand-500/30 transition-colors
                                {{ $tipe->tipe === 'A' ? 'border-l-4 border-l-accent-400' : ($tipe->tipe === 'B' ? 'border-l-4 border-l-brand-400' : 'border-l-4 border-l-gray-500') }}">
                        <div class="flex flex-wrap items-start justify-between gap-3 mb-3">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="w-8 h-8 rounded-lg flex items-center justify-center text-sm font-bold
                                        {{ $tipe->tipe === 'A' ? 'bg-accent-500/20 text-accent-400' : ($tipe->tipe === 'B' ? 'bg-brand-500/20 text-brand-400' : 'bg-gray-500/20 text-gray-400') }}">
                                        {{ $tipe->tipe }}
                                    </span>
                                    <div>
                                        <h3 class="font-semibold text-white">{{ $tipe->nama_tipe }}</h3>
                                        @if($tipe->ukuran_kamar)
                                            <span class="text-xs text-gray-400">{{ str_contains(strtolower($tipe->ukuran_kamar), 'm') ? $tipe->ukuran_kamar : $tipe->ukuran_kamar . ' m²' }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-xl font-bold text-brand-400">
                                    Rp {{ number_format($tipe->harga_bulanan, 0, ',', '.') }}
                                </div>
                                <span class="text-xs text-gray-500">/bulan</span>
                                @if($tipe->harga_tahunan)
                                    <div class="text-xs text-gray-500 mt-0.5">
                                        Tahunan: Rp {{ number_format($tipe->harga_tahunan, 0, ',', '.') }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if($tipe->deskripsi)
                            <p class="text-xs text-gray-400 mb-3">{{ $tipe->deskripsi }}</p>
                        @endif

                        {{-- Fasilitas per tipe --}}
                        @if($tipe->fasilitas && count($tipe->fasilitas) > 0)
                            <div class="flex flex-wrap gap-1.5 mb-3">
                                @foreach($tipe->fasilitas as $fas)
                                    <span class="px-2 py-1 rounded-lg text-xs bg-white/5 text-gray-300 flex items-center gap-1">
                                        @php $icon = \App\Models\TipeKamar::FASILITAS_ICONS[$fas] ?? 'check'; @endphp
                                        <i data-feather="{{ $icon }}" class="w-3 h-3 text-brand-400"></i>
                                        {{ $fas }}
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        {{-- Ketersediaan --}}
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full {{ $tipe->isAvailable() ? 'bg-brand-400' : 'bg-red-400' }}"></div>
                                <span class="text-xs {{ $tipe->isAvailable() ? 'text-brand-400' : 'text-red-400' }}">
                                    {{ $tipe->isAvailable() ? "{$tipe->kamar_tersedia} dari {$tipe->jumlah_kamar} kamar tersedia" : 'Penuh' }}
                                </span>
                            </div>
                            @if($tipe->isAvailable())
                                <button onclick="selectTipeKamar({{ $tipe->id }}, '{{ $tipe->nama_tipe }}', {{ $tipe->harga_bulanan }})"
                                        class="text-xs bg-brand-500/20 text-brand-400 px-3 py-1.5 rounded-lg hover:bg-brand-500/30 transition font-medium">
                                    Pilih Kamar
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="glass rounded-2xl p-6 text-center text-gray-400">
                        <p class="text-sm">Belum ada tipe kamar yang tersedia untuk kos ini.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Peta Lokasi Mini --}}
        <div class="glass rounded-2xl p-5 animate-slide-up">
            <h2 class="text-sm font-semibold text-gray-300 mb-3 flex items-center gap-2">
                <i data-feather="map" class="w-4 h-4 text-brand-400"></i>
                Lokasi
            </h2>
            <div id="detail-map" class="w-full h-48 rounded-xl overflow-hidden"></div>
        </div>

        {{-- Peraturan --}}
        @if($kos->peraturan)
            <div class="glass rounded-2xl p-5 animate-slide-up">
                <h2 class="text-sm font-semibold text-gray-300 mb-2 flex items-center gap-2">
                    <i data-feather="alert-triangle" class="w-4 h-4 text-accent-400"></i>
                    Peraturan Kos
                </h2>
                <p class="text-gray-400 text-sm leading-relaxed whitespace-pre-line">{{ $kos->peraturan }}</p>
            </div>
        @endif
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════
    FLOATING BOTTOM BAR (Sticky Footer)
    Berisi: Harga, Tombol Tanya CS AI, Tombol Booking
═══════════════════════════════════════════════════════ --}}
<div class="fixed bottom-0 left-0 right-0 z-50 glass border-t border-white/10 px-4 py-3 shadow-2xl">
    <div class="max-w-4xl mx-auto flex items-center justify-between gap-3">
        {{-- Harga --}}
        <div class="flex-shrink-0">
            <div class="text-xs text-gray-400">Mulai dari</div>
            <div class="text-lg font-bold text-brand-400">
                Rp {{ number_format($kos->harga_mulai ?? 0, 0, ',', '.') }}
                <span class="text-xs text-gray-500 font-normal">/bln</span>
            </div>
        </div>

        {{-- Buttons --}}
        <div class="flex items-center gap-2">
            {{-- Tombol Tanya CS AI --}}
            <button onclick="openAIChatWithContext({{ $kos->id }})"
                    class="flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-white/5 border border-white/10 text-gray-300 text-sm font-medium hover:bg-white/10 transition">
                <i data-feather="message-circle" class="w-4 h-4 text-accent-400"></i>
                <span class="hidden sm:inline">Tanya CS AI</span>
                <span class="sm:hidden">AI</span>
            </button>

            {{-- Tombol Booking (perlu login) --}}
            @auth
                <button id="btn-booking"
                        class="flex items-center gap-1.5 px-5 py-2.5 rounded-xl bg-gradient-to-r from-brand-500 to-brand-600 text-white text-sm font-semibold hover:shadow-lg hover:shadow-brand-500/25 transition-all active:scale-95">
                    <i data-feather="calendar" class="w-4 h-4"></i>
                    <span>Booking</span>
                </button>
            @else
                <a href="{{ route('login') }}?redirect={{ urlencode(request()->fullUrl()) }}"
                   class="flex items-center gap-1.5 px-5 py-2.5 rounded-xl bg-gradient-to-r from-brand-500 to-brand-600 text-white text-sm font-semibold hover:shadow-lg hover:shadow-brand-500/25 transition-all">
                    <i data-feather="log-in" class="w-4 h-4"></i>
                    <span>Login & Booking</span>
                </a>
            @endauth
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════
    BOOKING MODAL
═══════════════════════════════════════════════════════ --}}
@auth
<div id="booking-modal" class="fixed inset-0 z-[60] hidden">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeBookingModal()"></div>
    <div class="absolute bottom-0 left-0 right-0 glass rounded-t-3xl p-6 animate-slide-up max-h-[80vh] overflow-y-auto">
        <div class="w-12 h-1 bg-white/20 rounded-full mx-auto mb-4"></div>
        <h3 class="text-lg font-bold text-white mb-4">Konfirmasi Booking</h3>

        <form id="booking-form">
            @csrf
            <input type="hidden" name="tipe_kamar_id" id="booking-tipe-id">

            <div class="glass rounded-xl p-4 mb-4">
                <div class="text-sm text-gray-400">Tipe Kamar</div>
                <div class="text-white font-semibold" id="booking-tipe-nama">-</div>
                <div class="text-brand-400 font-bold mt-1" id="booking-tipe-harga">-</div>
            </div>

            <div class="mb-4">
                <label class="block text-xs font-medium text-gray-400 mb-2">Durasi Sewa</label>
                <select name="durasi" class="w-full bg-white/5 border border-white/10 rounded-xl px-3 py-2.5 text-sm text-gray-200 focus:border-brand-500 focus:outline-none">
                    <option value="bulanan">Bulanan</option>
                    <option value="tahunan">Tahunan</option>
                </select>
            </div>

            <div class="mb-4">
                <label for="booking-tanggal-mulai" class="block text-xs font-medium text-gray-400 mb-2">Tanggal Mulai Sewa</label>
                <input type="date"
                       id="booking-tanggal-mulai"
                       name="tanggal_mulai"
                       value="{{ date('Y-m-d') }}"
                       min="{{ date('Y-m-d') }}"
                       onclick="this.showPicker && this.showPicker()"
                       class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-gray-200 focus:border-brand-500 focus:outline-none transition cursor-pointer"
                       style="color-scheme: dark;"
                       required>
                <p class="text-[11px] text-gray-500 mt-1">📅 Klik untuk membuka kalender (minimal mulai hari ini).</p>
            </div>

            <div class="mb-4">
                <label class="block text-xs font-medium text-gray-400 mb-2">Catatan (opsional)</label>
                <textarea name="catatan" rows="2" placeholder="Catatan khusus..."
                          class="w-full bg-white/5 border border-white/10 rounded-xl px-3 py-2.5 text-sm text-gray-200 focus:border-brand-500 focus:outline-none resize-none"></textarea>
            </div>

            <button type="submit"
                    class="w-full bg-gradient-to-r from-brand-500 to-brand-600 text-white py-3 rounded-xl font-semibold hover:shadow-lg hover:shadow-brand-500/25 transition-all active:scale-95">
                🔒 Bayar Sekarang via Midtrans
            </button>
        </form>
    </div>
</div>
@endauth

{{-- Midtrans Snap.js (Sandbox) --}}
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ─── Mini Map ─────────────────────────────────────
    const detailMap = L.map('detail-map', { zoomControl: false, scrollWheelZoom: false })
        .setView([{{ $kos->latitude }}, {{ $kos->longitude }}], 16);

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(detailMap);

    L.marker([{{ $kos->latitude }}, {{ $kos->longitude }}]).addTo(detailMap)
        .bindPopup('<strong class="text-brand-400">{{ $kos->nama_kos }}</strong>').openPopup();

    feather.replace();
});

// ─── Booking Functions ────────────────────────────
let selectedTipeId = null;

function selectTipeKamar(id, nama, harga) {
    selectedTipeId = id;
    document.getElementById('booking-tipe-id').value = id;
    document.getElementById('booking-tipe-nama').textContent = nama;
    document.getElementById('booking-tipe-harga').textContent = 'Rp ' + Number(harga).toLocaleString('id-ID') + '/bulan';
    openBookingModal();
}

function openBookingModal() {
    document.getElementById('booking-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeBookingModal() {
    document.getElementById('booking-modal').classList.add('hidden');
    document.body.style.overflow = '';
}

@auth
// ─── Handle Booking Form Submission ───────────────
document.getElementById('btn-booking')?.addEventListener('click', function() {
    if (!selectedTipeId) {
        alert('Silakan pilih tipe kamar terlebih dahulu.');
        window.scrollTo({ top: document.querySelector('[data-feather="layers"]').offsetTop - 100, behavior: 'smooth' });
        return;
    }
    openBookingModal();
});

document.getElementById('booking-form')?.addEventListener('submit', async function(e) {
    e.preventDefault();

    const submitBtn = this.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn ? submitBtn.innerHTML : '';
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '⏳ Menghubungkan ke Midtrans...';
    }

    const formData = new FormData(this);

    try {
        const res = await fetch('{{ route("booking.create") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': window.CSRF_TOKEN,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(Object.fromEntries(formData)),
        });

        const data = await res.json();
        console.log('Midtrans Booking Response:', data);

        if (res.ok && data.success && data.snap_token) {
            closeBookingModal();

            // Buka Midtrans Snap popup
            window.snap.pay(data.snap_token, {
                onSuccess: function(result) {
                    console.log('Payment success:', result);
                    window.location.href = '/payment/status/' + data.order_id;
                },
                onPending: function(result) {
                    console.log('Payment pending:', result);
                    window.location.href = '/payment/status/' + data.order_id;
                },
                onError: function(result) {
                    console.error('Payment error:', result);
                    alert('Pembayaran gagal: ' + (result?.status_message || JSON.stringify(result)));
                },
                onClose: function() {
                    console.log('Payment popup closed');
                }
            });
        } else {
            // Ambil pesan error spesifik dari response JSON
            let errorMsg = data.message || data.error_detail;
            if (!errorMsg && data.errors) {
                errorMsg = Object.values(data.errors).flat().join(', ');
            }
            if (!errorMsg) {
                errorMsg = 'Terjadi kesalahan sistem saat memproses pembayaran Midtrans.';
            }

            console.error('Checkout Error Detail:', errorMsg, data);
            alert('Midtrans Error: ' + errorMsg);
        }
    } catch (err) {
        console.error('AJAX/Network Error:', err);
        alert('Kesalahan Koneksi / Request: ' + err.message);
    } finally {
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
        }
    }
});
@endauth

// ─── Open AI Chat with Kos Context ───────────────
function openAIChatWithContext(kosId) {
    // Trigger AI chat widget with kos context
    if (window.openAIChat) {
        window.openAIChat(kosId);
    }
}
</script>
@endsection
