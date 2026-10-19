<?php

namespace App\Http\Controllers;

use App\Models\Kos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * Controller Detail Kos.
 * Bisa diakses oleh Guest (tanpa login).
 */
class KosController extends Controller
{
    /**
     * Halaman detail kos dengan semua tipe kamar, foto, fasilitas.
     * KEAMANAN: Hanya kos approved yang bisa dilihat.
     */
    public function show(int $id)
    {
        $kos = Cache::remember("kos_detail:{$id}", 600, function () use ($id) {
            return Kos::approved()
                ->with([
                    'tipeKamar' => fn($q) => $q->orderBy('tipe'),
                    'owner:id,name,phone,avatar',
                    'subscription' => fn($q) => $q->active(),
                ])
                ->findOrFail($id);
        });

        $isVerified = $kos->status === 'approved';

        return view('kos.detail', compact('kos', 'isVerified'));
    }

    /**
     * API: Pencarian kos (digunakan oleh AJAX/peta).
     */
    public function search(Request $request)
    {
        $request->validate([
            'q' => 'nullable|string|max:100',
            'kampus' => 'nullable|string',
            'radius' => 'nullable|numeric|min:0.5|max:10',
            'tipe_kos' => 'nullable|in:putra,putri,campur',
            'sort' => 'nullable|in:harga_asc,harga_desc,rating,jarak',
        ]);

        $query = Kos::approved()->with('tipeKamar')->adaKamarKosong();

        // Pencarian teks
        if ($request->filled('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('nama_kos', 'LIKE', "%{$search}%")
                  ->orWhere('alamat', 'LIKE', "%{$search}%")
                  ->orWhere('kecamatan', 'LIKE', "%{$search}%");
            });
        }

        // Filter radius kampus
        if ($request->filled('kampus') && isset(Kos::KAMPUS_COORDS[$request->kampus])) {
            $coords = Kos::KAMPUS_COORDS[$request->kampus];
            $query->withinRadius($coords['lat'], $coords['lng'], $request->input('radius', 2.0));
        }

        // Filter tipe kos
        if ($request->filled('tipe_kos')) {
            $query->where('tipe_kos', $request->tipe_kos);
        }

        // Sorting
        match ($request->input('sort')) {
            'rating' => $query->orderByDesc('rating_avg'),
            'harga_asc' => $query->orderByRaw('(SELECT MIN(harga_bulanan) FROM tipe_kamar WHERE tipe_kamar.kos_id = kos.id) ASC'),
            'harga_desc' => $query->orderByRaw('(SELECT MIN(harga_bulanan) FROM tipe_kamar WHERE tipe_kamar.kos_id = kos.id) DESC'),
            default => $query->orderByDesc('rating_avg'),
        };

        $results = $query->paginate(12);

        if ($request->expectsJson()) {
            return response()->json($results);
        }

        return view('kos.search', compact('results'));
    }
}
