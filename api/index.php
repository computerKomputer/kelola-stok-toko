<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
ini_set('log_errors', '1');
error_reporting(E_ALL);

register_shutdown_function(function () {
    $error = error_get_last();
    if ($error !== null) {
        error_log('FATAL PHP: ' . json_encode($error));
    }
});

require __DIR__ . '/../public/index.php';
