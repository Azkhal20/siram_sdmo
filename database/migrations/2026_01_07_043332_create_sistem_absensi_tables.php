<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Kedeputian
        Schema::create('kedeputians', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kedeputian'); // Contoh: Kedeputian Sinka, Mutasi, dll.
            $table->timestamps();
        });

        // 2. Tabel Peserta Magang
        Schema::create('peserta_magang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kedeputian_id')->constrained('kedeputians')->onDelete('cascade');
            $table->string('nama');
            $table->string('nomor_induk')->unique(); // NIM atau ID Magang
            $table->timestamps();
        });

        // 3. Tabel Absensi (Data Utama dari PDF)
        Schema::create('absensis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peserta_magang_id')->constrained('peserta_magang')->onDelete('cascade');
            $table->date('tanggal');
            $table->string('kode', 10); // TK, TM, PC
            $table->time('jam_masuk')->nullable();
            $table->time('jam_pulang')->nullable();
            $table->integer('menit_telat')->default(0);
            $table->text('keterangan')->nullable();
            $table->unique(['peserta_magang_id', 'tanggal']); // Mencegah duplikasi data absensi per hari
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensis');
        Schema::dropIfExists('peserta_magang');
        Schema::dropIfExists('kedeputians');
    }
};
