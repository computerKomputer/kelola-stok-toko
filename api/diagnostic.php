<?php

header('Content-Type: application/json');

$host = 'gateway01.ap-southeast-1.prod.aws.tidbcloud.com';

echo json_encode([
    'php_version' => PHP_VERSION,
    'host' => $host,
    'resolved_ip' => gethostbyname($host),
    'dns_records' => dns_get_record($host, DNS_A),
], JSON_PRETTY_PRINT);