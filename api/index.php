<?php
// api/index.php (Versi Minimalis & Paling Stabil)

// Setup storage folder di /tmp (Wajib untuk Vercel)
foreach (['/tmp/storage/framework/views', '/tmp/storage/framework/sessions', '/tmp/storage/framework/cache'] as $path) {
    if (!is_dir($path)) mkdir($path, 0755, true);
}

// Setting environment agar tidak crash
putenv('APP_STORAGE=/tmp/storage');
putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');
putenv('SESSION_DRIVER=cookie');
putenv('CACHE_STORE=array');
putenv('LOG_CHANNEL=stderr');

// Muat aplikasi
require __DIR__ . '/../public/index.php';
