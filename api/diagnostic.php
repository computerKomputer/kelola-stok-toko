<?php

header('Content-Type: application/json');

$result = [
    'autoload' => false,
    'bootstrap' => false,
    'http_kernel' => false,
    'error' => null,
];

try {
    require __DIR__ . '/../vendor/autoload.php';
    $result['autoload'] = true;

    $app = require __DIR__ . '/../bootstrap/app.php';
    $result['bootstrap'] = true;

    $kernel = $app->make(
        Illuminate\Contracts\Http\Kernel::class
    );

    $kernel->bootstrap();
    $result['http_kernel'] = true;
} catch (Throwable $e) {
    $result['error'] = get_class($e) . ': ' . $e->getMessage();
}

echo json_encode($result, JSON_PRETTY_PRINT);