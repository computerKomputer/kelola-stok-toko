<?php

header('Content-Type: application/json');

$host = 'gateway01.ap-southeast-1.prod.aws.tidbcloud.com';
$port = 4000;

$start = microtime(true);
$socket = @fsockopen($host, $port, $errno, $errstr, 5);

$result = [
    'host' => $host,
    'port' => $port,
    'connected' => $socket !== false,
    'duration_seconds' => round(microtime(true) - $start, 2),
    'error' => $socket ? null : $errstr,
];

if ($socket) {
    fclose($socket);
}

echo json_encode($result, JSON_PRETTY_PRINT);