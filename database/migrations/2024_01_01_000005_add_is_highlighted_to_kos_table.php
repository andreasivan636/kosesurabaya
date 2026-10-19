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
        if (!Schema::hasColumn('kos', 'is_highlighted')) {
            Schema::table('kos', function (Blueprint $table) {
                $table->boolean('is_highlighted')->default(false)->after('status')->comment('Status penanda Kos Premium berkedip di peta');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('kos', 'is_highlighted')) {
            Schema::table('kos', function (Blueprint $table) {
                $table->dropColumn('is_highlighted');
            });
        }
    }
};
