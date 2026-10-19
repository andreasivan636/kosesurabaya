<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel Subscription Pemilik Kos.
     * Pemilik yang berlangganan mendapat marker glowing di peta.
     */
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade')
                  ->comment('Pemilik kos (role: owner)');

            $table->foreignId('kos_id')
                  ->constrained('kos')
                  ->onDelete('cascade');

            // Paket subscription
            $table->enum('paket', ['basic', 'premium', 'ultimate'])->default('basic');
            $table->unsignedInteger('harga')->comment('Harga langganan dalam Rupiah');

            // Durasi aktif
            $table->date('mulai_aktif');
            $table->date('berakhir');
            $table->boolean('is_active')->default(true);

            // Pembayaran subscription
            $table->string('order_id')->unique();
            $table->string('snap_token')->nullable();
            $table->enum('status_bayar', ['pending', 'paid', 'expired'])->default('pending');

            // Fitur subscription
            $table->boolean('marker_glow')->default(false)->comment('Marker bersinar di peta');
            $table->boolean('prioritas_pencarian')->default(false)->comment('Muncul di urutan atas');
            $table->unsignedSmallInteger('max_foto')->default(5)->comment('Jumlah max foto yang bisa diupload');

            $table->timestamps();

            $table->index(['kos_id', 'is_active']);
            $table->index('berakhir');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
