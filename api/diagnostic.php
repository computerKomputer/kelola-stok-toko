<?php

header('Content-Type: application/json');

$host = getenv('DB_HOST');
$port = getenv('DB_PORT') ?: 4000;
$database = getenv('DB_DATABASE');
$username = getenv('DB_USERNAME');
$password = getenv('DB_PASSWORD');

try {
    $pdo = new PDO(
        "mysql:host={$host};port={$port};dbname={$database}",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 5,
            PDO::MYSQL_ATTR_SSL_CA => dirname(__DIR__) . '/isrgrootx1.pem',
        ]
    );

    echo json_encode([
        'connected' => true,
        'database' => $pdo->query('SELECT DATABASE()')->fetchColumn(),
    ], JSON_PRETTY_PRINT);

} catch (Throwable $e) {
    echo json_encode([
        'connected' => false,
        'error_type' => get_class($e),
        'error_code' => $e->getCode(),
        'error_message' => $e->getMessage(),
    ], JSON_PRETTY_PRINT);
}