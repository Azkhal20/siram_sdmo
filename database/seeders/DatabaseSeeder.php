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
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin SDMO',
            'email' => 'admin@bkn.go.id',
            'password' => bcrypt('password'),
        ]);

        $kedeputians = [
            'Kedeputian Bidang Sistem Informasi Kepegawaian',
            'Kedeputian Bidang Mutasi Kepegawaian',
            'Kedeputian Bidang Pengembangan Kompetensi',
            'Kedeputian Bidang Pembinaan Manajemen Kepegawaian',
            'Kedeputian Bidang Pengawasan dan Pengendalian',
        ];

        foreach ($kedeputians as $nama) {
            Kedeputian::create(['nama_kedeputian' => $nama]);
        }
    }
}
