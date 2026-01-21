<?php

// 1. Paksa Laravel menggunakan folder /tmp karena Vercel bersifat read-only
// Kita buat struktur folder yang dibutuhkan Laravel di dalam /tmp
$tmpFolders = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/framework/cache',
    '/tmp/storage/logs',
];

foreach ($tmpFolders as $folder) {
    if (!is_dir($folder)) {
        mkdir($folder, 0755, true);
    }
}

// 2. Hubungkan ke file startup standar Laravel
require __DIR__ . '/../public/index.php';
