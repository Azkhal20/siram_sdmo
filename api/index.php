<?php

// 1. Definisikan folder penyimpanan sementara di /tmp (satu-satunya folder yang bisa ditulis di Vercel)
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

// 2. Beritahu Laravel untuk menggunakan path baru ini sebelum bootstrap berjalan
putenv("VIEW_COMPILED_PATH=" . $storagePath . "/framework/views");
putenv("SESSION_DRIVER=cookie");
putenv("LOG_CHANNEL=stderr");

// 3. Masuk ke aplikasi Laravel
require __DIR__ . '/../public/index.php';
