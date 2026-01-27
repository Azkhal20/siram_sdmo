<?php

// 1. Setup Folder Storage di /tmp
$storageTemplates = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/framework/cache',
    '/tmp/storage/logs'
];
foreach ($storageTemplates as $path) {
    if (!is_dir($path)) {
        mkdir($path, 0755, true);
    }
}

// 2. Setting wajib untuk Vercel
putenv('SESSION_DRIVER=cookie');
putenv('LOG_CHANNEL=stderr');
putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');
putenv('CACHE_DRIVER=array');
putenv('QUEUE_CONNECTION=sync');

// 3. Set base path untuk Laravel
$_SERVER['SCRIPT_FILENAME'] = __DIR__ . '/../public/index.php';

// 4. Masuk ke Laravel
require __DIR__ . '/../public/index.php';