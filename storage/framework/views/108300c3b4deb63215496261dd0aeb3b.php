<?php $__env->startSection('title', 'KoSeSurabaya - Peta Kos Mahasiswa Surabaya'); ?>

<?php $__env->startSection('styles'); ?>
<style>
    #map-container { height: calc(100vh - 60px); }
    #map { height: 100%; width: 100%; z-index: 1; }

    /* Custom Leaflet popup */
    .leaflet-popup-content-wrapper {
        background: rgba(15, 23, 42, 0.95) !important;
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: #e2e8f0 !important;
        border-radius: 16px !important;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5) !important;
    }
    .leaflet-popup-tip { background: rgba(15, 23, 42, 0.95) !important; }
    .leaflet-popup-close-button { color: #94a3b8 !important; }

    /* Keyframe Kelap-Kelip Khusus untuk Marker Kos Premium */
    @keyframes premium-beacon {
        0%, 100% {
            transform: scale(1);
            box-shadow: 0 0 12px 3px rgba(245, 158, 11, 0.9), 0 0 22px 6px rgba(234, 179, 8, 0.6);
            opacity: 1;
        }
        50% {
            transform: scale(1.15);
            box-shadow: 0 0 24px 8px rgba(251, 191, 36, 1), 0 0 38px 12px rgba(245, 158, 11, 0.9);
            opacity: 0.85;
        }
    }

    .premium-marker-glow {
        animation: premium-beacon 1.2s ease-in-out infinite;
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="relative" id="map-container">
    
    <div id="map"></div>

    
    <div class="absolute top-4 left-4 z-[1000] w-80 max-w-[calc(100vw-2rem)]" x-data="{ showFilters: true }">
        
        <button @click="showFilters = !showFilters"
                class="md:hidden glass px-4 py-2 rounded-xl text-sm flex items-center gap-2 mb-2 hover:bg-white/10 transition">
            <i data-feather="filter" class="w-4 h-4"></i>
            <span x-text="showFilters ? 'Sembunyikan' : 'Filter Pencarian'"></span>
        </button>

        <div x-show="showFilters" x-transition class="glass rounded-2xl p-5 shadow-2xl">
            <h2 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                <i data-feather="search" class="w-5 h-5 text-brand-400"></i>
                Cari Kos
            </h2>

            
            <div class="mb-4">
                <label class="block text-xs font-medium text-gray-400 mb-2">📍 Dekat Kampus</label>
                <select id="filter-kampus" class="w-full bg-white/5 border border-white/10 rounded-xl px-3 py-2.5 text-sm text-gray-200 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 focus:outline-none transition">
                    <option value="">Semua Lokasi</option>
                    <?php $__currentLoopData = $kampusList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kode => $kampus): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($kode); ?>"><?php echo e($kampus['nama']); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            
            <div class="mb-4">
                <label class="block text-xs font-medium text-gray-400 mb-2">
                    📐 Radius: <span id="radius-value" class="text-brand-400 font-bold">2.0</span> km
                </label>
                <input type="range" id="filter-radius" min="0.5" max="5" step="0.5" value="2"
                       class="w-full h-2 bg-white/10 rounded-lg appearance-none cursor-pointer accent-brand-500">
            </div>

            
            <div class="mb-4">
                <label class="block text-xs font-medium text-gray-400 mb-2">🏠 Tipe Kos</label>
                <div class="flex gap-2">
                    <button data-tipe="" class="filter-tipe-btn flex-1 px-3 py-2 rounded-xl text-xs font-medium bg-brand-500/20 text-brand-400 border border-brand-500/30 transition hover:bg-brand-500/30">Semua</button>
                    <button data-tipe="putra" class="filter-tipe-btn flex-1 px-3 py-2 rounded-xl text-xs font-medium bg-white/5 text-gray-400 border border-white/10 transition hover:bg-white/10">Putra</button>
                    <button data-tipe="putri" class="filter-tipe-btn flex-1 px-3 py-2 rounded-xl text-xs font-medium bg-white/5 text-gray-400 border border-white/10 transition hover:bg-white/10">Putri</button>
                    <button data-tipe="campur" class="filter-tipe-btn flex-1 px-3 py-2 rounded-xl text-xs font-medium bg-white/5 text-gray-400 border border-white/10 transition hover:bg-white/10">Campur</button>
                </div>
            </div>

            
            <div class="mb-4">
                <label class="block text-xs font-medium text-gray-400 mb-2">💰 Range Harga /bulan</label>
                <div class="flex gap-2">
                    <input type="number" id="filter-harga-min" placeholder="Min"
                           class="flex-1 bg-white/5 border border-white/10 rounded-xl px-3 py-2 text-sm text-gray-200 focus:border-brand-500 focus:outline-none">
                    <span class="text-gray-500 self-center">—</span>
                    <input type="number" id="filter-harga-max" placeholder="Max"
                           class="flex-1 bg-white/5 border border-white/10 rounded-xl px-3 py-2 text-sm text-gray-200 focus:border-brand-500 focus:outline-none">
                </div>
            </div>

            
            <button id="btn-search" class="w-full bg-gradient-to-r from-brand-500 to-brand-600 text-white py-3 rounded-xl font-semibold text-sm hover:shadow-lg hover:shadow-brand-500/25 transition-all active:scale-95 flex items-center justify-center gap-2">
                <i data-feather="search" class="w-4 h-4"></i>
                Cari Kos
            </button>
        </div>
    </div>

    
    <div class="absolute bottom-6 left-4 z-[1000] glass rounded-xl px-4 py-3 shadow-xl">
        <div class="flex items-center gap-4 text-xs text-gray-400">
            <div class="flex items-center gap-1.5">
                <div class="w-3 h-3 bg-brand-500 rounded-full"></div>
                <span>Kos Biasa</span>
            </div>
            <div class="flex items-center gap-1.5">
                <div class="w-4 h-4 rounded-full bg-amber-400 text-slate-900 font-black flex items-center justify-center text-[10px] animate-pulse">!</div>
                <span class="text-amber-300 font-semibold">Kos Premium (!)</span>
            </div>
        </div>
    </div>

    
    <div class="absolute top-4 right-4 z-[1000] glass rounded-xl px-4 py-2.5 shadow-xl">
        <div class="flex items-center gap-2">
            <i data-feather="home" class="w-4 h-4 text-brand-400"></i>
            <span class="text-sm text-gray-300">
                <span id="kos-count" class="font-bold text-white">0</span> kos ditemukan
            </span>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ─── Inisialisasi Peta Surabaya ───────────────────
    const map = L.map('map', {
        zoomControl: false,
    }).setView([-7.2756, 112.7522], 13);

    L.control.zoom({ position: 'bottomright' }).addTo(map);

    // Tile layer OpenStreetMap (100% gratis)
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    // Koordinat kampus untuk radius filter (marker tidak ditampilkan ke peta sesuai permintaan)
    const kampusCoords = <?php echo json_encode($kampusList, 15, 512) ?>;

    // ─── Marker Kos ───────────────────────────────────
    let kosMarkerGroup = L.layerGroup().addTo(map);
    let radiusCircle = null;
    let selectedTipe = '';

    // Custom icon builder (Menampilkan ikon ! dengan efek kelap-kelip untuk Kos Premium)
    function createKosIcon(kos) {
        const isPremium = Boolean(kos.is_premium || kos.glow);

        if (isPremium) {
            // Kos Premium: Tanda seru (!) dengan animasi kelap-kelip mencolok
            return L.divIcon({
                html: `
                    <div class="relative flex items-center justify-center cursor-pointer group">
                        <!-- Ripple ping ring -->
                        <span class="absolute -inset-1 rounded-full bg-amber-400 opacity-75 animate-ping"></span>
                        <!-- Pulsing beacon badge -->
                        <div class="relative flex items-center justify-center w-8 h-8 rounded-full bg-gradient-to-tr from-amber-500 via-yellow-400 to-amber-300 border-2 border-white premium-marker-glow animate-pulse shadow-lg transform group-hover:scale-125 transition-transform">
                            <span class="text-slate-900 font-black text-base leading-none select-none">!</span>
                        </div>
                        <!-- Star tag -->
                        <div class="absolute -top-1 -right-1 w-3.5 h-3.5 bg-red-600 text-white text-[8px] font-black rounded-full flex items-center justify-center border border-white shadow">
                            ★
                        </div>
                    </div>
                `,
                className: '',
                iconSize: [34, 34],
                iconAnchor: [17, 17],
                popupAnchor: [0, -20],
            });
        }

        // Regular Kos Marker
        return L.divIcon({
            html: `
                <div class="flex items-center justify-center w-8 h-8 bg-brand-600 rounded-full border-2 border-brand-400 shadow-lg cursor-pointer hover:scale-110 transition-transform">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </div>
            `,
            className: '',
            iconSize: [32, 32],
            iconAnchor: [16, 16],
            popupAnchor: [0, -20],
        });
    }

    // Popup content builder
    function buildPopup(kos) {
        const isPremium = Boolean(kos.is_premium || kos.glow);
        const ratingStars = '★'.repeat(Math.round(kos.rating)) + '☆'.repeat(5 - Math.round(kos.rating));
        const harga = kos.harga_mulai ? `Rp ${Number(kos.harga_mulai).toLocaleString('id-ID')}` : 'N/A';
        const foto = kos.foto_utama || '';
        const fotoHtml = foto
            ? `<img src="${foto}" class="w-full h-28 object-cover rounded-lg mb-2" alt="${kos.nama}">`
            : '';

        return `
            <div class="w-56 p-1">
                ${isPremium ? '<div class="mb-1 text-center py-0.5 px-2 bg-gradient-to-r from-amber-500 to-yellow-600 text-white font-bold text-[10px] rounded-md tracking-wider uppercase shadow-sm">⭐ KOS PREMIUM REKOMENDASI</div>' : ''}
                ${fotoHtml}
                <h3 class="font-bold text-sm text-white mb-1">${kos.nama}</h3>
                <p class="text-xs text-gray-400 mb-1.5">${kos.alamat}</p>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-accent-400 text-xs">${ratingStars} ${kos.rating}</span>
                    ${kos.verified ? '<span class="text-xs bg-brand-500/20 text-brand-400 px-1.5 py-0.5 rounded-md">✓ Verified</span>' : ''}
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-brand-400 font-bold text-sm">${harga}/bln</span>
                    <span class="text-xs text-gray-400">${kos.kamar_kosong} kamar kosong</span>
                </div>
                ${kos.jarak ? `<p class="text-xs text-gray-500 mt-1">📍 ${kos.jarak.toFixed(1)} km</p>` : ''}
                <a href="/kos/${kos.id}" class="mt-2 block text-center ${isPremium ? 'bg-gradient-to-r from-amber-500 to-yellow-600 font-bold' : 'bg-brand-500 font-medium'} text-white text-xs py-2 rounded-lg hover:opacity-90 transition">
                    Lihat Detail →
                </a>
            </div>
        `;
    }

    // ─── Load Data Kos ────────────────────────────────
    async function loadKosMarkers() {
        const kampus = document.getElementById('filter-kampus').value;
        const radius = document.getElementById('filter-radius').value;
        const hargaMin = document.getElementById('filter-harga-min').value;
        const hargaMax = document.getElementById('filter-harga-max').value;

        const params = new URLSearchParams();
        if (kampus) params.append('kampus', kampus);
        if (radius) params.append('radius', radius);
        if (selectedTipe) params.append('tipe_kos', selectedTipe);
        if (hargaMin) params.append('harga_min', hargaMin);
        if (hargaMax) params.append('harga_max', hargaMax);

        try {
            const res = await fetch(`<?php echo e(route('api.kos.markers')); ?>?${params}`, {
                headers: { 'Accept': 'application/json' }
            });
            const data = await res.json();

            // Clear existing markers
            kosMarkerGroup.clearLayers();

            // Clear existing radius circle
            if (radiusCircle) {
                map.removeLayer(radiusCircle);
                radiusCircle = null;
            }

            // Tampilkan radius circle
            if (kampus && kampusCoords[kampus]) {
                const coords = kampusCoords[kampus];
                radiusCircle = L.circle([coords.lat, coords.lng], {
                    radius: radius * 1000,
                    color: '#22c55e',
                    fillColor: '#22c55e',
                    fillOpacity: 0.08,
                    weight: 2,
                    dashArray: '8, 8',
                }).addTo(map);

                map.fitBounds(radiusCircle.getBounds(), { padding: [20, 20] });
            }

            // Tambah markers kos
            if (data.data) {
                data.data.forEach(kos => {
                    const marker = L.marker([kos.lat, kos.lng], {
                        icon: createKosIcon(kos),
                    }).bindPopup(buildPopup(kos), {
                        maxWidth: 280,
                    });

                    kosMarkerGroup.addLayer(marker);
                });

                document.getElementById('kos-count').textContent = data.data.length;
            }
        } catch (err) {
            console.error('Error loading markers:', err);
        }
    }

    // ─── Event Listeners ──────────────────────────────
    document.getElementById('btn-search').addEventListener('click', loadKosMarkers);

    document.getElementById('filter-radius').addEventListener('input', function() {
        document.getElementById('radius-value').textContent = this.value;
    });

    // Tipe filter buttons
    document.querySelectorAll('.filter-tipe-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            selectedTipe = this.dataset.tipe;
            document.querySelectorAll('.filter-tipe-btn').forEach(b => {
                b.className = 'filter-tipe-btn flex-1 px-3 py-2 rounded-xl text-xs font-medium bg-white/5 text-gray-400 border border-white/10 transition hover:bg-white/10';
            });
            this.className = 'filter-tipe-btn flex-1 px-3 py-2 rounded-xl text-xs font-medium bg-brand-500/20 text-brand-400 border border-brand-500/30 transition hover:bg-brand-500/30';
        });
    });

    // Initial load
    loadKosMarkers();

    // Re-render feather icons
    feather.replace();
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\KoSesurabaya\resources\views/map/index.blade.php ENDPATH**/ ?>