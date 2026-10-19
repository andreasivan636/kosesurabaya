<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel Kos - Data properti kos milik owner.
     * Status: pending (menunggu verifikasi admin), approved, rejected
     */
    public function up(): void
    {
        Schema::create('kos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade')
                  ->comment('Pemilik kos (role: owner)');

            // Informasi dasar
            $table->string('nama_kos');
            $table->text('alamat');
            $table->string('kecamatan')->nullable();
            $table->string('kelurahan')->nullable();
            $table->enum('tipe_kos', ['putra', 'putri', 'campur'])->default('campur');

            // Koordinat untuk Leaflet.js
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);

            // Deskripsi & media
            $table->text('deskripsi')->nullable();
            $table->json('foto')->nullable()->comment('Array path foto kos');
            $table->string('foto_utama')->nullable();

            // Fasilitas umum kos (bukan per-kamar)
            $table->json('fasilitas_umum')->nullable()->comment('Parkir, Dapur Bersama, dll');

            // Peraturan
            $table->text('peraturan')->nullable();
            $table->string('kontak_pemilik', 20)->nullable();

            // Status verifikasi
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('alasan_reject')->nullable();
            $table->timestamp('approved_at')->nullable();

            // Rating agregat (dihitung dari review)
            $table->decimal('rating_avg', 2, 1)->default(0);
            $table->unsignedInteger('rating_count')->default(0);

            // Kampus terdekat (untuk fitur filter radius)
            $table->string('kampus_terdekat')->nullable();
            $table->decimal('jarak_kampus_km', 5, 2)->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Index untuk pencarian
            $table->index(['status', 'tipe_kos']);
            $table->index(['latitude', 'longitude']);
            $table->index('kampus_terdekat');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kos');
    }
};
