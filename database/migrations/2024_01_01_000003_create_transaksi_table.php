<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel Transaksi Booking Kos (via Midtrans Sandbox).
     */
    public function up(): void
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->string('order_id')->unique()->comment('Format: KSS-{timestamp}-{userId}');

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade')
                  ->comment('Customer yang booking');

            $table->foreignId('tipe_kamar_id')
                  ->constrained('tipe_kamar')
                  ->onDelete('cascade');

            $table->foreignId('kos_id')
                  ->constrained('kos')
                  ->onDelete('cascade');

            // Detail pembayaran
            $table->unsignedInteger('jumlah_bayar')->comment('Nominal dalam Rupiah');
            $table->enum('durasi', ['bulanan', 'tahunan'])->default('bulanan');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');

            // Midtrans
            $table->string('snap_token')->nullable()->comment('Token Snap Midtrans');
            $table->string('payment_type')->nullable()->comment('bank_transfer, gopay, dll');
            $table->enum('status', [
                'pending',
                'waiting_payment',
                'paid',
                'expired',
                'cancelled',
                'refunded'
            ])->default('pending');
            $table->json('midtrans_response')->nullable()->comment('Raw response dari callback');
            $table->timestamp('paid_at')->nullable();

            // Info tambahan
            $table->text('catatan')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'status']);
            $table->index('order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
