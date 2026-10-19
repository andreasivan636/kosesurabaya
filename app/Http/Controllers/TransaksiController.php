<?php

namespace App\Http\Controllers;

use App\Models\Kos;
use App\Models\TipeKamar;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Controller Transaksi & Pembayaran Midtrans.
 * KEAMANAN: Server Key hanya ada di .env, tidak pernah ke frontend.
 */
class TransaksiController extends Controller
{
    public function __construct()
    {
        $this->initMidtrans();
    }

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
     * Buat booking & generate Snap Token Midtrans.
     * Customer HARUS login (middleware auth).
     */
    public function createBooking(Request $request)
    {
        $validated = $request->validate([
            'tipe_kamar_id' => 'required|exists:tipe_kamar,id',
            'durasi' => 'required|in:bulanan,tahunan',
            'tanggal_mulai' => 'required|date|after_or_equal:today',
            'catatan' => 'nullable|string|max:500',
        ]);

        $tipeKamar = TipeKamar::with('kos')->findOrFail($validated['tipe_kamar_id']);

        // Validasi: kos harus approved & kamar tersedia
        if ($tipeKamar->kos->status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Kos ini belum diverifikasi oleh admin.',
            ], 422);
        }
        if ($tipeKamar->kamar_tersedia <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Maaf, tipe kamar ini sudah penuh.',
            ], 422);
        }

        // Hitung jumlah bayar
        $jumlahBayar = $validated['durasi'] === 'tahunan' && $tipeKamar->harga_tahunan
            ? $tipeKamar->harga_tahunan
            : $tipeKamar->harga_bulanan;

        // Hitung tanggal selesai
        $tanggalMulai = \Carbon\Carbon::parse($validated['tanggal_mulai']);
        $tanggalSelesai = $validated['durasi'] === 'tahunan'
            ? $tanggalMulai->copy()->addYear()
            : $tanggalMulai->copy()->addMonth();

        $user = Auth::user();
        $orderId = Transaksi::generateOrderId($user->id);

        // Buat transaksi
        $transaksi = Transaksi::create([
            'order_id' => $orderId,
            'user_id' => $user->id,
            'tipe_kamar_id' => $tipeKamar->id,
            'kos_id' => $tipeKamar->kos_id,
            'jumlah_bayar' => $jumlahBayar,
            'durasi' => $validated['durasi'],
            'tanggal_mulai' => $tanggalMulai,
            'tanggal_selesai' => $tanggalSelesai,
            'status' => 'pending',
            'catatan' => $validated['catatan'],
        ]);

        // Generate Snap Token Midtrans
        try {
            $this->initMidtrans();

            $snapToken = \Midtrans\Snap::getSnapToken([
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => $jumlahBayar,
                ],
                'customer_details' => [
                    'first_name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone ?? '',
                ],
                'item_details' => [[
                    'id' => $tipeKamar->id,
                    'price' => $jumlahBayar,
                    'quantity' => 1,
                    'name' => substr("Kos: {$tipeKamar->kos->nama_kos} - {$tipeKamar->nama_tipe}", 0, 50),
                ]],
            ]);

            $transaksi->update([
                'snap_token' => $snapToken,
                'status' => 'waiting_payment',
            ]);

            return response()->json([
                'success' => true,
                'snap_token' => $snapToken,
                'order_id' => $orderId,
            ]);

        } catch (\Throwable $e) {
            Log::error('Midtrans Snap Error: ' . $e->getMessage(), [
                'order_id' => $orderId,
                'exception' => $e,
            ]);

            if (isset($transaksi)) {
                $transaksi->update(['status' => 'cancelled']);
            }

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error_detail' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Callback/Notification Handler dari Midtrans.
     * Endpoint ini menerima webhook dari Midtrans.
     */
    public function handleNotification(Request $request)
    {
        try {
            $notification = new \Midtrans\Notification();

            $orderId = $notification->order_id;
            $statusCode = $notification->status_code;
            $transactionStatus = $notification->transaction_status;
            $fraudStatus = $notification->fraud_status;
            $paymentType = $notification->payment_type;

            $transaksi = Transaksi::where('order_id', $orderId)->firstOrFail();

            // Verifikasi signature Midtrans
            $signatureKey = hash('sha512',
                $orderId . $statusCode . $notification->gross_amount . config('services.midtrans.server_key')
            );

            if ($signatureKey !== $notification->signature_key) {
                Log::warning("Midtrans signature mismatch for order: {$orderId}");
                return response()->json(['message' => 'Invalid signature'], 403);
            }

            // Update status berdasarkan response Midtrans
            if ($transactionStatus === 'capture' || $transactionStatus === 'settlement') {
                if ($fraudStatus === 'accept' || $fraudStatus === null) {
                    $transaksi->update([
                        'status' => 'paid',
                        'payment_type' => $paymentType,
                        'paid_at' => now(),
                        'midtrans_response' => (array) $notification,
                    ]);

                    // Kurangi kamar tersedia
                    $tipeKamar = $transaksi->tipeKamar;
                    if ($tipeKamar->kamar_tersedia > 0) {
                        $tipeKamar->decrement('kamar_tersedia');
                    }
                }
            } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
                $transaksi->update([
                    'status' => $transactionStatus === 'expire' ? 'expired' : 'cancelled',
                    'midtrans_response' => (array) $notification,
                ]);
            }

            return response()->json(['message' => 'OK']);

        } catch (\Exception $e) {
            Log::error('Midtrans Notification Error: ' . $e->getMessage());
            return response()->json(['message' => 'Error'], 500);
        }
    }

    /**
     * Halaman status pembayaran untuk customer.
     */
    public function paymentStatus(string $orderId)
    {
        $transaksi = Transaksi::where('order_id', $orderId)
            ->where('user_id', Auth::id())
            ->with(['kos:id,nama_kos', 'tipeKamar:id,tipe,nama_tipe'])
            ->firstOrFail();

        return view('transaksi.status', compact('transaksi'));
    }
}
