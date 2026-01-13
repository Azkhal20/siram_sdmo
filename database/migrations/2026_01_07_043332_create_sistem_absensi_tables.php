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
        // 1. Tabel Kedeputian (Master Data Unit Eselon I)
        // Relasi: One Kedeputian -> Many Peserta Magang
        Schema::create('kedeputians', function (Blueprint $table) {
            $table->id(); // Primary Key (PK)
            $table->string('nama');
            $table->timestamps();
        });

        // 2. Tabel Peserta Magang
        // Relasi: Belongs To Kedeputian
        Schema::create('peserta_magang', function (Blueprint $table) {
            $table->id(); // Primary Key (PK)

            // Foreign Key (FK) ke tabel kedeputians
            // onDelete('cascade') artinya jika Kedeputian dihapus, peserta di dalamnya ikut terhapus
            $table->foreignId('kedeputian_id')->constrained('kedeputians')->onDelete('cascade');

            $table->string('nama');

            // UNIQUE KEY: Nomor Induk tidak boleh kembar antar peserta
            $table->string('nomor_induk')->unique();

            // Kolom tambahan untuk menyimpan teks Unit Kerja asli dari PDF (sebagai referensi detail)
            $table->string('unit_kerja_text')->nullable();

            $table->timestamps();
        });

        // 3. Tabel Absensi
        // Relasi: Belongs To Peserta Magang
        Schema::create('absensis', function (Blueprint $table) {
            $table->id(); // Primary Key (PK)

            // Foreign Key (FK) ke tabel peserta_magang
            $table->foreignId('peserta_magang_id')->constrained('peserta_magang')->onDelete('cascade');

            $table->date('tanggal'); // Tipe data DATE biasa, bukan FK

            $table->string('kehadiran'); // RENAMED from 'kode'
            $table->string('jam_masuk')->nullable();
            $table->string('jam_pulang')->nullable();
            $table->integer('menit_telat')->default(0);
            $table->string('keterangan')->nullable(); // Kolom baru untuk catatan tambahan

            $table->timestamps();

            // COMPOSITE UNIQUE KEY
            // Mencegah duplikasi data absen untuk peserta yang sama di tanggal yang sama.
            // Rule: "Setiap peserta hanya memiliki 1 absen setiap harinya"
            $table->unique(['peserta_magang_id', 'tanggal']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensis');
        Schema::dropIfExists('peserta_magang');
        Schema::dropIfExists('kedeputians');
    }
};
