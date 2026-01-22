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
// 2. Paksa konfigurasi kritis untuk Vercel
putenv('APP_CONFIG_CACHE=/tmp/config.php');
putenv('APP_SERVICES_CACHE=/tmp/services.php');
putenv('APP_PACKAGES_CACHE=/tmp/packages.php');
putenv('APP_ROUTES_CACHE=/tmp/routes.php');
// Matikan caching yang mencoba menulis ke disk read-only
putenv('CONFIG_CACHE=false');
putenv('ROUTES_CACHE=false');
// 3. Masuk ke Laravel
require __DIR__ . '/../public/index.php';