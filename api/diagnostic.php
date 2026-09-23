<?php

header('Content-Type: application/json');

echo json_encode([
    'php_version' => PHP_VERSION,
    'pdo_mysql' => extension_loaded('pdo_mysql'),
    'openssl' => extension_loaded('openssl'),
    'storage_writable' => is_writable('/tmp'),
], JSON_PRETTY_PRINT);