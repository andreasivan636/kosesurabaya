<?php

use App\Http\Controllers\AIChatController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KosController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\TransaksiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (untuk Flutter Mobile App)
|--------------------------------------------------------------------------
| Base URL: http://{IP_LOKAL_LAPTOP}:8000/api
| Autentikasi: Laravel Sanctum (Bearer Token)
|--------------------------------------------------------------------------
*/

// ─── PUBLIC API (Tanpa Auth) ──────────────────────
Route::prefix('v1')->group(function () {

    // Auth
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // Kos & Peta (bisa diakses tanpa login)
    Route::get('/kos/markers', [MapController::class, 'getKosMarkers']);
    Route::get('/kos/search', [KosController::class, 'search']);
    Route::get('/kos/{id}', [KosController::class, 'show']);

    // AI Chat (bisa diakses tanpa login)
    Route::post('/ai/chat', [AIChatController::class, 'sendMessage']);

    // Kampus coordinates
    Route::get('/kampus', function () {
        return response()->json([
            'success' => true,
            'data' => \App\Models\Kos::KAMPUS_COORDS,
        ]);
    });

    // Midtrans Webhook
    Route::post('/midtrans/notification', [TransaksiController::class, 'handleNotification']);
});

// ─── PROTECTED API (Perlu Auth Sanctum) ───────────
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {

    // User profile
    Route::get('/user', function (Request $request) {
        return response()->json([
            'success' => true,
            'user' => $request->user(),
        ]);
    });

    Route::post('/logout', [AuthController::class, 'logout']);

    // Booking (Customer harus login)
    Route::post('/booking', [TransaksiController::class, 'createBooking']);
    Route::get('/payment/status/{orderId}', [TransaksiController::class, 'paymentStatus']);

    // Transaksi history
    Route::get('/transaksi', function (Request $request) {
        $transaksi = $request->user()->transaksi()
            ->with(['kos:id,nama_kos', 'tipeKamar:id,tipe,nama_tipe'])
            ->latest()
            ->paginate(10);
        return response()->json($transaksi);
    });
});
