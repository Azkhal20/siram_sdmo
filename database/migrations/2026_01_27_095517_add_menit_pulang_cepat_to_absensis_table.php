<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            // Tambahkan kolom baru setelah menit_telat
            if (!Schema::hasColumn('absensis', 'menit_pulang_cepat')) {
                $table->integer('menit_pulang_cepat')->default(0)->after('menit_telat');
            }
        });
    }

    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->dropColumn('menit_pulang_cepat');
        });
    }
};