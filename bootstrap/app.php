<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->registered(function ($app) {
        if (isset($_SERVER['VERCEL_URL'])) {
            $storagePath = '/tmp/storage';
            $app->useStoragePath($storagePath);

            $dirs = [
                $storagePath . '/framework/views',
                $storagePath . '/framework/sessions',
                $storagePath . '/framework/cache',
                $storagePath . '/framework/cache/data',
                $storagePath . '/logs',
            ];

            foreach ($dirs as $dir) {
                if (!is_dir($dir)) {
                    mkdir($dir, 0755, true);
                }
            }
        }
    })
    ->create();

return $app;
