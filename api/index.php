<?php

// 1. Setup Folder Storage di /tmp (PAKSA)
$storagePath = '/tmp/storage/framework/';
if (!is_dir($storagePath . 'views')) {
    mkdir($storagePath . 'views', 0755, true);
    mkdir($storagePath . 'sessions', 0755, true);
    mkdir($storagePath . 'cache', 0755, true);
}

// 2. Kirim pesan ke Laravel agar menggunakan folder ini
putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');
putenv('SESSION_DRIVER=cookie');

// 3. Masuk ke Laravel
require __DIR__ . '/../public/index.php';
