<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
ini_set('log_errors', '1');
error_reporting(E_ALL);

set_exception_handler(function (Throwable $e) {
    error_log('VERCEL_EXCEPTION: ' . get_class($e) . ' | ' . $e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
});

register_shutdown_function(function () {
    $error = error_get_last();
    if ($error !== null) {
        error_log('VERCEL_FATAL: ' . json_encode($error));
    }
});

require __DIR__ . '/../public/index.php';
