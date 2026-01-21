<?php

// 1. Setup folder penyimpanan di /tmp
$storagePath = '/tmp/storage';
$dirs = [
    $storagePath . '/framework/views',
    $storagePath . '/framework/sessions',
    $storagePath . '/framework/cache',
    $storagePath . '/framework/cache/data',
    $storagePath . '/bootstrap/cache',
    $storagePath . '/logs',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// 2. Jalankan Migrasi Otomatis (Hanya jika belum ada file 'migrated' di /tmp)
// Ini adalah cara aman agar migrasi tidak memperlambat setiap request
if (!file_exists('/tmp/migrated')) {
    try {
        putenv('ARTISAN_MIGRATE=true');
        shell_exec('php ' . __DIR__ . '/../artisan migrate --force');
        file_put_contents('/tmp/migrated', time());
    } catch (\Exception $e) {
        // Abaikan jika gagal, aplikasi akan melempar error DB nanti
    }
}

// 3. Masuk ke aplikasi Laravel
require __DIR__ . '/../public/index.php';
