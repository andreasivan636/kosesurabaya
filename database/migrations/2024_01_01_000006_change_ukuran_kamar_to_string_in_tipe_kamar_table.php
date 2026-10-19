<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tipe_kamar', function (Blueprint $table) {
            $table->string('ukuran_kamar', 50)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tipe_kamar', function (Blueprint $table) {
            $table->decimal('ukuran_kamar', 5, 1)->nullable()->change();
        });
    }
};
