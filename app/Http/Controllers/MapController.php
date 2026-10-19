<?php

namespace App\Http\Controllers;

use App\Models\Kos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * Controller utama: Peta & Pencarian Radius.
 * Bisa diakses oleh Guest (tanpa login).
 */
class MapController extends Controller
{
    /**
     * Halaman utama dengan peta Leaflet.js.
     */
    public function index()
    {
        $kampusList = Kos::KAMPUS_COORDS;

        return view('map.index', compact('kampusList'));
    }

    /**
     * API: Ambil data kos untuk ditampilkan di peta.
     * Menggunakan Redis cache untuk mempercepat query.
     * KEAMANAN: Hanya kos berstatus 'approved' yang ditampilkan.
     */
    public function getKosMarkers(Request $request)
    {
        $request->validate([
            'kampus' => 'nullable|string|in:UNTAG,ITS,UNAIR,UNESA',
            'radius' => 'nullable|numeric|min:0.5|max:10',
            'tipe_kos' => 'nullable|string|in:putra,putri,campur',
            'harga_min' => 'nullable|integer|min:0',
            'harga_max' => 'nullable|integer|min:0',
        ]);

        $kampus = $request->input('kampus');
        $radius = $request->input('radius', 2.0);
        $tipeKos = $request->input('tipe_kos');
        $hargaMin = $request->input('harga_min');
        $hargaMax = $request->input('harga_max');

        // Cache key berdasarkan parameter filter
        $cacheKey = "kos_markers:{$kampus}:{$radius}:{$tipeKos}:{$hargaMin}:{$hargaMax}";

        $markers = Cache::remember($cacheKey, 300, function () use (
            $kampus, $radius, $tipeKos, $hargaMin, $hargaMax
        ) {
            $query = Kos::approved()
                ->with(['tipeKamar:id,kos_id,tipe,harga_bulanan,kamar_tersedia,fasilitas', 'subscription'])
                ->adaKamarKosong();

            // Filter radius dari kampus
            if ($kampus && isset(Kos::KAMPUS_COORDS[$kampus])) {
                $coords = Kos::KAMPUS_COORDS[$kampus];
                $query->withinRadius($coords['lat'], $coords['lng'], $radius);
            }

            // Filter tipe kos
            if ($tipeKos) {
                $query->where('tipe_kos', $tipeKos);
            }

            // Filter harga
            if ($hargaMin || $hargaMax) {
                $query->whereHas('tipeKamar', function ($q) use ($hargaMin, $hargaMax) {
                    if ($hargaMin) $q->where('harga_bulanan', '>=', $hargaMin);
                    if ($hargaMax) $q->where('harga_bulanan', '<=', $hargaMax);
                });
            }

            return $query->get()->map(function ($kos) {
                $isPremium = $kos->hasActiveSubscription() || !empty($kos->is_highlighted);
                return [
                    'id' => $kos->id,
                    'nama' => $kos->nama_kos,
                    'alamat' => $kos->alamat,
                    'lat' => (float) $kos->latitude,
                    'lng' => (float) $kos->longitude,
                    'tipe' => $kos->tipe_kos,
                    'foto_utama' => $kos->foto_utama ? asset('storage/' . $kos->foto_utama) : null,
                    'rating' => (float) $kos->rating_avg,
                    'harga_mulai' => $kos->harga_mulai,
                    'kamar_kosong' => $kos->total_kamar_kosong,
                    'verified' => true, // Sudah pasti approved
                    'glow' => $isPremium, // Marker animasi untuk subscriber / premium
                    'is_premium' => $isPremium,
                    'jarak' => $kos->jarak ?? null,
                    'fasilitas_preview' => $kos->tipeKamar->first()?->fasilitas ?? [],
                ];
            });
        });

        return response()->json([
            'success' => true,
            'data' => $markers,
            'kampus_coords' => Kos::KAMPUS_COORDS,
        ]);
    }
}
