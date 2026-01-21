<?php

// Pastikan folder storage ada di /tmp (Wajib untuk Vercel)
$storagePath = '/tmp/storage/framework/views';
if (!is_dir($storagePath)) {
    mkdir($storagePath, 0755, true);
}

// Teruskan ke aplikasi Laravel
require __DIR__ . '/../public/index.php';
