<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Kedeputian;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $kedeputians = [
            "Sektetariat Utama (Sestama)",
            "Kedeputian Bidang Penyelenggaraan Layanan Manajemen ASN (PLM)",
            "Kedeputian Bidang Pembinaan Penyelenggaraan Manajemen ASN (PPM)",
            "Kedeputian Bidang Pengawasan dan Pengendalian Manajemen ASN (Wasdal)",
            "Kedeputian Bidang Sistem Informasi dan Digitalisasi Manajemen ASN  (Sidigi)",
            "Pusat-Pusat dan Inspektorat",
            "Kantor Regional I BKN Yogyakarta",
            "Kantor Regional II BKN Surabaya",
            "Kantor Regional III BKN Bandung",
            "Kantor Regional IV BKN Makassar",
            "Kantor Regional V BKN Jakarta",
            "Kantor Regional VI BKN Medan",
            "Kantor Regional VII BKN Palembang",
            "Kantor Regional VIII BKN Banjarbaru",
            "Kantor Regional IX BKN Jayapura",
            "Kantor Regional X BKN Denpasar",
            "Kantor Regional XI BKN Manado",
            "Kantor Regional XII BKN Pekanbaru",
            "Kantor Regional XIII BKN Banda Aceh",
            "Kantor Regional XIV BKN Manokwari"
        ];

        foreach ($kedeputians as $nama) {
            // Gunakan firstOrCreate agar tidak menduplikat data yang sudah ada
            Kedeputian::firstOrCreate(['nama' => $nama]);
        }
    }
}
