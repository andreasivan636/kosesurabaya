<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel Tipe Kamar - Setiap kos WAJIB memiliki 3 tipe: A, B, C
     * Masing-masing tipe punya harga & fasilitas berbeda.
     */
    public function up(): void
    {
        Schema::create('tipe_kamar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kos_id')
                  ->constrained('kos')
                  ->onDelete('cascade');

            $table->enum('tipe', ['A', 'B', 'C'])->comment('Tipe A=Premium, B=Standard, C=Ekonomi');
            $table->string('nama_tipe')->comment('Nama custom, misal: Deluxe, Standard, Ekonomi');
            $table->text('deskripsi')->nullable();

            // Harga
            $table->unsignedInteger('harga_bulanan')->comment('Harga per bulan dalam Rupiah');
            $table->unsignedInteger('harga_tahunan')->nullable()->comment('Harga per tahun (opsional diskon)');

            // Kapasitas
            $table->unsignedSmallInteger('jumlah_kamar')->default(1)->comment('Total kamar tipe ini');
            $table->unsignedSmallInteger('kamar_tersedia')->default(1)->comment('Kamar yang masih kosong');
            $table->string('ukuran_kamar', 50)->nullable()->comment('Ukuran kamar, misal: 3x4, 3*4, atau 12 m²');

            // Fasilitas per-tipe kamar (JSON array)
            $table->json('fasilitas')->nullable()->comment('["AC","WiFi","Kamar Mandi Dalam","Kasur"]');

            // Foto khusus tipe kamar
            $table->json('foto')->nullable();

            $table->timestamps();

            // Setiap kos hanya boleh punya 1 tipe A, 1 tipe B, 1 tipe C
            $table->unique(['kos_id', 'tipe']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tipe_kamar');
    }
};
