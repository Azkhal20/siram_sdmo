<?php

// 1. Setup Folder Storage di /tmp (Wajib untuk Vercel)
$storagePath = '/tmp/storage';
$subDirs = [
    '/framework/views',
    '/framework/sessions',
    '/framework/cache',
    '/framework/cache/data',
    '/logs'
];

foreach ($subDirs as $dir) {
    if (!is_dir($storagePath . $dir)) {
        mkdir($storagePath . $dir, 0755, true);
    }
}

// 2. Jalankan Migrasi Database secara otomatis
// Kita gunakan file penanda agar migrasi tidak berjalan terus-menerus
if (!file_exists('/tmp/migrated')) {
    try {
        // Memanggil artisan migrate dari dalam PHP
        exec('php ' . __DIR__ . '/../artisan migrate --force');
        file_put_contents('/tmp/migrated', time());
    } catch (\Exception $e) {
        // Jika gagal, biarkan Laravel yang menangani error DB nantinya
    }
}

// 3. Panggil Laravel
require __DIR__ . '/../public/index.php';
