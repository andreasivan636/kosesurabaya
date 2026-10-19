<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AIChatController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KosController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\TransaksiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| KoSeSurabaya Routes
|--------------------------------------------------------------------------
| Role-based routing dengan middleware ketat:
| - Guest: peta, pencarian, detail kos, AI chat
| - Customer (auth): booking, pembayaran
| - Owner (auth + role:owner): dashboard pemilik
| - Admin (auth + role:admin): dashboard admin
|--------------------------------------------------------------------------
*/

// ─── PUBLIC ROUTES (Guest / Tanpa Login) ──────────────
Route::get('/', [MapController::class, 'index'])->name('home');
Route::get('/api/kos/markers', [MapController::class, 'getKosMarkers'])->name('api.kos.markers');
Route::get('/kos/{id}', [KosController::class, 'show'])->name('kos.detail');
Route::get('/kos', [KosController::class, 'search'])->name('kos.search');

// AI Customer Service (bisa diakses tanpa login)
Route::post('/api/ai/chat', [AIChatController::class, 'sendMessage'])->name('api.ai.chat');

// ─── AUTH ROUTES ──────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ─── CUSTOMER ROUTES (Auth Required) ─────────────────
Route::middleware('auth')->group(function () {
    // Booking & Pembayaran
    Route::post('/booking', [TransaksiController::class, 'createBooking'])->name('booking.create');
    Route::get('/payment/status/{orderId}', [TransaksiController::class, 'paymentStatus'])->name('payment.status');
});

// Midtrans Webhook (tanpa auth, tapi diverifikasi signature)
Route::post('/api/midtrans/notification', [TransaksiController::class, 'handleNotification'])
    ->name('midtrans.notification')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

// ─── OWNER ROUTES (Auth + Role: owner) ───────────────
Route::middleware(['auth', 'role:owner'])->prefix('owner')->name('owner.')->group(function () {
    Route::get('/dashboard', [OwnerController::class, 'dashboard'])->name('dashboard');

    // Form Wizard Pendaftaran Kos
    Route::get('/kos/create/step-1', [OwnerController::class, 'createStep1'])->name('kos.create.step1');
    Route::post('/kos/create/step-1', [OwnerController::class, 'storeStep1'])->name('kos.store.step1');
    Route::get('/kos/create/step-2', [OwnerController::class, 'createStep2'])->name('kos.create.step2');
    Route::post('/kos/create/step-2', [OwnerController::class, 'storeStep2'])->name('kos.store.step2');
    Route::get('/kos/create/step-3', [OwnerController::class, 'createStep3'])->name('kos.create.step3');
    Route::post('/kos/create/step-3', [OwnerController::class, 'storeStep3'])->name('kos.store.step3');

    // Berlangganan Premium Kos
    Route::post('/kos/{id}/subscribe', [OwnerController::class, 'subscribePremium'])->name('kos.subscribe');
    Route::post('/kos/{id}/activate-premium', [OwnerController::class, 'activatePremium'])->name('kos.activatePremium');
});

// ─── ADMIN ROUTES (Auth + Role: admin) ───────────────
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/kos/pending', [AdminController::class, 'pendingKos'])->name('kos.pending');
    Route::post('/kos/{id}/approve', [AdminController::class, 'approveKos'])->name('kos.approve');
    Route::post('/kos/{id}/reject', [AdminController::class, 'rejectKos'])->name('kos.reject');
    Route::get('/transaksi', [AdminController::class, 'transaksi'])->name('transaksi');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::post('/users/{id}/toggle', [AdminController::class, 'toggleUser'])->name('users.toggle');
});
