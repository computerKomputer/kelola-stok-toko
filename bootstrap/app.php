<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$basePath = dirname(__DIR__);

// Konfigurasi khusus ketika aplikasi berjalan di Vercel.
if (getenv('VERCEL')) {

    // Arahkan logging ke STDERR, bukan storage/logs/laravel.log.
    $_ENV['LOG_CHANNEL'] = 'stderr';
    $_SERVER['LOG_CHANNEL'] = 'stderr';
    putenv('LOG_CHANNEL=stderr');

    // Gunakan direktori sementara untuk cache Laravel.
    $cachePath = '/tmp/laravel-bootstrap-cache';

    if (!is_dir($cachePath)) {
        mkdir($cachePath, 0777, true);
    }

    $_ENV['APP_CONFIG_CACHE'] = $cachePath . '/config.php';
    $_ENV['APP_ROUTES_CACHE'] = $cachePath . '/routes.php';
    $_ENV['APP_EVENTS_CACHE'] = $cachePath . '/events.php';
    $_ENV['APP_SERVICES_CACHE'] = $cachePath . '/services.php';
    $_ENV['APP_PACKAGES_CACHE'] = $cachePath . '/packages.php';

    putenv('APP_CONFIG_CACHE=' . $cachePath . '/config.php');
    putenv('APP_ROUTES_CACHE=' . $cachePath . '/routes.php');
    putenv('APP_EVENTS_CACHE=' . $cachePath . '/events.php');
    putenv('APP_SERVICES_CACHE=' . $cachePath . '/services.php');
    putenv('APP_PACKAGES_CACHE=' . $cachePath . '/packages.php');
}

return Application::configure(basePath: $basePath)
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up'
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);

        $middleware->redirectGuestsTo('/login');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->report(function (\Throwable $e): void {
            error_log(
                'ORIGINAL_EXCEPTION: ' . get_class($e)
                . ' | ' . $e->getMessage()
                . ' | ' . $e->getFile() . ':' . $e->getLine()
            );
        });
    })
    ->create();