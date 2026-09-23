<?php

header('Content-Type: application/json');

$result = [
    'php_version' => PHP_VERSION,
    'autoload' => false,
    'bootstrap' => false,
    'error' => null,
];

try {
    require __DIR__ . '/../vendor/autoload.php';
    $result['autoload'] = true;

    $app = require __DIR__ . '/../bootstrap/app.php';
    $result['bootstrap'] = true;
    $result['application_class'] = get_class($app);
} catch (Throwable $e) {
    $result['error'] = get_class($e) . ': ' . $e->getMessage();
}

echo json_encode($result, JSON_PRETTY_PRINT);