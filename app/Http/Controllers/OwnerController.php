<?php

namespace App\Http\Controllers;

use App\Models\Kos;
use App\Models\Subscription;
use App\Models\TipeKamar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Controller Dashboard Pemilik Kos.
 * Dilindungi middleware: auth + role:owner
 */
class OwnerController extends Controller
{
    /**
     * Inisialisasi konfigurasi Midtrans SDK dari config / .env.
     */
    private function initMidtrans(): void
    {
        \Midtrans\Config::$serverKey = config('services.midtrans.server_key', env('MIDTRANS_SERVER_KEY'));
        \Midtrans\Config::$clientKey = config('services.midtrans.client_key', env('MIDTRANS_CLIENT_KEY'));
        \Midtrans\Config::$isProduction = (bool) config('services.midtrans.is_production', env('MIDTRANS_IS_PRODUCTION', false));
        \Midtrans\Config::$isSanitized = (bool) config('services.midtrans.is_sanitized', env('MIDTRANS_IS_SANITIZED', true));
        \Midtrans\Config::$is3ds = (bool) config('services.midtrans.is_3ds', env('MIDTRANS_IS_3DS', true));
    }
    /**
     * Dashboard utama pemilik.
     */
    public function dashboard()
    {
        $user = Auth::user();
        $kosList = Kos::where('user_id', $user->id)
            ->withCount(['transaksi as booking_count' => fn($q) => $q->where('status', 'paid')])
            ->with('tipeKamar')
            ->latest()
            ->get();

        $stats = [
            'total_kos' => $kosList->count(),
            'total_kamar' => $kosList->sum(fn($k) => $k->tipeKamar->sum('jumlah_kamar')),
            'kamar_terisi' => $kosList->sum(fn($k) => $k->tipeKamar->sum('jumlah_kamar') - $k->tipeKamar->sum('kamar_tersedia')),
            'total_booking' => $kosList->sum('booking_count'),
        ];

        return view('owner.dashboard', compact('kosList', 'stats'));
    }

    /**
     * Form wizard pendaftaran kos (langkah 1: info dasar).
     */
    public function createStep1()
    {
        return view('owner.kos.create-step1');
    }

    /**
     * Simpan langkah 1 & lanjut ke langkah 2.
     */
    public function storeStep1(Request $request)
    {
        $validated = $request->validate([
            'nama_kos' => 'required|string|max:255',
            'alamat' => 'required|string',
            'kecamatan' => 'required|string|max:100',
            'kelurahan' => 'nullable|string|max:100',
            'tipe_kos' => 'required|in:putra,putri,campur',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'deskripsi' => 'nullable|string|max:2000',
            'kontak_pemilik' => 'required|string|max:20',
            'peraturan' => 'nullable|string|max:2000',
        ]);

        // Simpan ke session untuk wizard multi-step
        $request->session()->put('kos_wizard.step1', $validated);

        return redirect()->route('owner.kos.create.step2');
    }

    /**
     * Form wizard langkah 2: Upload foto.
     */
    public function createStep2()
    {
        if (!session()->has('kos_wizard.step1')) {
            return redirect()->route('owner.kos.create.step1')
                ->with('error', 'Silakan isi data dasar terlebih dahulu.');
        }

        return view('owner.kos.create-step2');
    }

    /**
     * Simpan langkah 2 & lanjut ke langkah 3.
     */
    public function storeStep2(Request $request)
    {
        $request->validate([
            'foto_utama' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'foto.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'fasilitas_umum' => 'nullable|array',
            'fasilitas_umum.*' => 'string',
        ]);

        $fotoUtama = $request->file('foto_utama')->store('kos/foto', 'public');
        $fotoLain = [];
        if ($request->hasFile('foto')) {
            foreach ($request->file('foto') as $file) {
                $fotoLain[] = $file->store('kos/foto', 'public');
            }
        }

        $request->session()->put('kos_wizard.step2', [
            'foto_utama' => $fotoUtama,
            'foto' => $fotoLain,
            'fasilitas_umum' => $request->input('fasilitas_umum', []),
        ]);

        return redirect()->route('owner.kos.create.step3');
    }

    /**
     * Form wizard langkah 3: Tipe Kamar (WAJIB 3 tipe: A, B, C).
     */
    public function createStep3()
    {
        if (!session()->has('kos_wizard.step2')) {
            return redirect()->route('owner.kos.create.step2')
                ->with('error', 'Silakan upload foto terlebih dahulu.');
        }

        return view('owner.kos.create-step3');
    }

    /**
     * Simpan semua data wizard & buat kos baru.
     * Dinamis: Minimal 1 dan Maksimal 2 tipe kamar.
     * Status awal: "pending" (menunggu verifikasi admin).
     */
    public function storeStep3(Request $request)
    {
        if (!session()->has('kos_wizard.step1') || !session()->has('kos_wizard.step2')) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Sesi pendaftaran telah kedaluwarsa. Silakan muat ulang atau isi formulir dari langkah 1.',
                    'errors' => ['session' => ['Sesi pendaftaran telah kedaluwarsa. Silakan mulai kembali dari langkah 1.']]
                ], 422);
            }
            return redirect()->route('owner.kos.create.step1')
                ->with('error', 'Sesi pendaftaran telah kedaluwarsa. Silakan isi form dari awal.');
        }

        $rules = [
            // Tipe 1 (A - Wajib)
            'tipe_a_nama' => 'required|string|max:100',
            'tipe_a_harga_bulanan' => 'required|integer|min:100000',
            'tipe_a_harga_tahunan' => 'nullable|integer|min:1000000',
            'tipe_a_jumlah_kamar' => 'required|integer|min:1',
            'tipe_a_kamar_tersedia' => 'required|integer|min:0',
            'tipe_a_ukuran' => 'nullable|string|max:50',
            'tipe_a_fasilitas' => 'required|array|min:1',
            'tipe_a_fasilitas.*' => 'string',
            'tipe_a_deskripsi' => 'nullable|string|max:500',
        ];

        // Tipe 2 (B - Opsional, hanya jika tipe_b_enabled bernilai true / 1)
        $hasTipeB = $request->boolean('tipe_b_enabled') || $request->input('tipe_b_enabled') === '1';
        if ($hasTipeB) {
            $rules = array_merge($rules, [
                'tipe_b_nama' => 'required|string|max:100',
                'tipe_b_harga_bulanan' => 'required|integer|min:100000',
                'tipe_b_harga_tahunan' => 'nullable|integer|min:1000000',
                'tipe_b_jumlah_kamar' => 'required|integer|min:1',
                'tipe_b_kamar_tersedia' => 'required|integer|min:0',
                'tipe_b_ukuran' => 'nullable|string|max:50',
                'tipe_b_fasilitas' => 'required|array|min:1',
                'tipe_b_fasilitas.*' => 'string',
                'tipe_b_deskripsi' => 'nullable|string|max:500',
            ]);
        }

        $messages = [
            'required' => ':attribute wajib diisi.',
            'min' => ':attribute minimal bernilai :min.',
            'max' => ':attribute maksimal :max karakter.',
            'integer' => ':attribute harus berupa angka.',
            'array' => ':attribute harus berupa pilihan yang valid.',
            'tipe_a_fasilitas.required' => 'Fasilitas kamar Tipe 1 wajib dipilih minimal 1.',
            'tipe_b_fasilitas.required' => 'Fasilitas kamar Tipe 2 wajib dipilih minimal 1.',
            'tipe_a_fasilitas.min' => 'Fasilitas kamar Tipe 1 wajib dipilih minimal 1.',
            'tipe_b_fasilitas.min' => 'Fasilitas kamar Tipe 2 wajib dipilih minimal 1.',
        ];

        $attributes = [
            'tipe_a_nama' => 'Nama Tipe Kamar 1',
            'tipe_a_harga_bulanan' => 'Harga/Bulan Tipe 1',
            'tipe_a_harga_tahunan' => 'Harga/Tahun Tipe 1',
            'tipe_a_jumlah_kamar' => 'Jumlah Kamar Tipe 1',
            'tipe_a_kamar_tersedia' => 'Kamar Kosong Tipe 1',
            'tipe_a_ukuran' => 'Ukuran Kamar Tipe 1',
            'tipe_a_fasilitas' => 'Fasilitas Tipe 1',
            'tipe_a_deskripsi' => 'Deskripsi Tipe 1',
            'tipe_b_nama' => 'Nama Tipe Kamar 2',
            'tipe_b_harga_bulanan' => 'Harga/Bulan Tipe 2',
            'tipe_b_harga_tahunan' => 'Harga/Tahun Tipe 2',
            'tipe_b_jumlah_kamar' => 'Jumlah Kamar Tipe 2',
            'tipe_b_kamar_tersedia' => 'Kamar Kosong Tipe 2',
            'tipe_b_ukuran' => 'Ukuran Kamar Tipe 2',
            'tipe_b_fasilitas' => 'Fasilitas Tipe 2',
            'tipe_b_deskripsi' => 'Deskripsi Tipe 2',
        ];

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $rules, $messages, $attributes);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Terdapat kesalahan input pada formulir.',
                    'errors' => $validator->errors(),
                ], 422);
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $step1 = session('kos_wizard.step1');
        $step2 = session('kos_wizard.step2');

        // Buat kos baru - status: pending
        $kos = Kos::create([
            'user_id' => Auth::id(),
            ...$step1,
            'foto_utama' => $step2['foto_utama'],
            'foto' => $step2['foto'],
            'fasilitas_umum' => $step2['fasilitas_umum'],
            'status' => 'pending', // Menunggu verifikasi admin
        ]);

        // Simpan Tipe Kamar 1 (Tipe A)
        TipeKamar::create([
            'kos_id' => $kos->id,
            'tipe' => 'A',
            'nama_tipe' => $request->input('tipe_a_nama'),
            'deskripsi' => $request->input('tipe_a_deskripsi'),
            'harga_bulanan' => $request->input('tipe_a_harga_bulanan'),
            'harga_tahunan' => $request->input('tipe_a_harga_tahunan'),
            'jumlah_kamar' => $request->input('tipe_a_jumlah_kamar'),
            'kamar_tersedia' => $request->input('tipe_a_kamar_tersedia'),
            'ukuran_kamar' => $request->input('tipe_a_ukuran'),
            'fasilitas' => $request->input('tipe_a_fasilitas'),
        ]);

        // Simpan Tipe Kamar 2 (Tipe B) jika diisi
        if ($hasTipeB) {
            TipeKamar::create([
                'kos_id' => $kos->id,
                'tipe' => 'B',
                'nama_tipe' => $request->input('tipe_b_nama'),
                'deskripsi' => $request->input('tipe_b_deskripsi'),
                'harga_bulanan' => $request->input('tipe_b_harga_bulanan'),
                'harga_tahunan' => $request->input('tipe_b_harga_tahunan'),
                'jumlah_kamar' => $request->input('tipe_b_jumlah_kamar'),
                'kamar_tersedia' => $request->input('tipe_b_kamar_tersedia'),
                'ukuran_kamar' => $request->input('tipe_b_ukuran'),
                'fasilitas' => $request->input('tipe_b_fasilitas'),
            ]);
        }

        // Hapus session wizard
        $request->session()->forget('kos_wizard');

        // Invalidate cache
        try {
            Cache::flush();
        } catch (\Throwable $e) {}

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Berhasil! Kos Anda telah didaftarkan dan saat ini sedang menunggu verifikasi Admin.',
                'redirect_url' => route('owner.dashboard'),
            ]);
        }

        return redirect()->route('owner.dashboard')
            ->with('success', 'Berhasil! Kos Anda telah didaftarkan dan saat ini sedang menunggu verifikasi Admin.');
    }

    /**
     * Buat transaksi langganan Premium Kos & generate Midtrans Snap Token.
     */
    public function subscribePremium(Request $request, int $id)
    {
        $user = Auth::user();
        $kos = Kos::where('user_id', $user->id)->findOrFail($id);

        if ($kos->status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Kos harus berstatus Disetujui sebelum dapat berlangganan premium.',
            ], 422);
        }

        if ($kos->isPremium()) {
            return response()->json([
                'success' => false,
                'message' => 'Kos ini sudah berstatus Premium.',
            ], 422);
        }

        $harga = 50000; // Tagihan simulasi Rp 50.000 / bulan
        $orderId = 'PREM-' . $kos->id . '-' . time() . '-' . strtoupper(Str::random(4));

        try {
            $this->initMidtrans();

            $snapToken = \Midtrans\Snap::getSnapToken([
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => $harga,
                ],
                'customer_details' => [
                    'first_name' => $user->name,
                    'email' => $user->email,
                    'phone' => $kos->kontak_pemilik ?? ($user->phone ?? ''),
                ],
                'item_details' => [[
                    'id' => 'SUB-PREMIUM-' . $kos->id,
                    'price' => $harga,
                    'quantity' => 1,
                    'name' => substr("Langganan Premium Kos: {$kos->nama_kos}", 0, 50),
                ]],
            ]);

            Subscription::updateOrCreate(
                ['kos_id' => $kos->id, 'status_bayar' => 'pending'],
                [
                    'user_id' => $user->id,
                    'paket' => 'premium',
                    'harga' => $harga,
                    'mulai_aktif' => now(),
                    'berakhir' => now()->addDays(30),
                    'order_id' => $orderId,
                    'snap_token' => $snapToken,
                    'status_bayar' => 'pending',
                    'is_active' => false,
                    'marker_glow' => true,
                    'prioritas_pencarian' => true,
                    'max_foto' => 15,
                ]
            );

            return response()->json([
                'success' => true,
                'snap_token' => $snapToken,
                'order_id' => $orderId,
                'kos_id' => $kos->id,
            ]);
        } catch (\Throwable $e) {
            Log::error('Midtrans Premium Subscription Error: ' . $e->getMessage(), [
                'kos_id' => $kos->id,
                'exception' => $e,
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error_detail' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Aktivasi status premium kos setelah pembayaran Midtrans berhasil (onSuccess callback).
     */
    public function activatePremium(Request $request, int $id)
    {
        $user = Auth::user();
        $kos = Kos::where('user_id', $user->id)->findOrFail($id);

        // Update is_highlighted kos menjadi true
        $kos->update(['is_highlighted' => true]);

        // Update / create subscription aktif
        $sub = Subscription::where('kos_id', $kos->id)
            ->where('user_id', $user->id)
            ->latest()
            ->first();

        if ($sub) {
            $sub->update([
                'status_bayar' => 'paid',
                'is_active' => true,
                'mulai_aktif' => now(),
                'berakhir' => now()->addDays(30),
                'marker_glow' => true,
                'prioritas_pencarian' => true,
            ]);
        } else {
            Subscription::create([
                'user_id' => $user->id,
                'kos_id' => $kos->id,
                'paket' => 'premium',
                'harga' => 50000,
                'mulai_aktif' => now(),
                'berakhir' => now()->addDays(30),
                'is_active' => true,
                'order_id' => $request->input('order_id', 'PREM-' . $kos->id . '-' . time()),
                'status_bayar' => 'paid',
                'marker_glow' => true,
                'prioritas_pencarian' => true,
                'max_foto' => 15,
            ]);
        }

        // Flush cache agar marker di peta langsung update
        try {
            Cache::forget("kos_detail:{$kos->id}");
            Cache::flush();
        } catch (\Throwable $e) {
            Log::warning('Cache flush error: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => "Selamat! Kos \"{$kos->nama_kos}\" kini berstatus Premium dan akan menyala kelap-kelip di peta utama!",
        ]);
    }
}
