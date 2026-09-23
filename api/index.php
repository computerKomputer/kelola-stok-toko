<?php

ini_set('display_errors', '0');
ini_set('log_errors', '1');
error_reporting(E_ALL);

register_shutdown_function(function () {
    $error = error_get_last();

    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
        error_log(
            'CAKRAWALA_FATAL: ' .
            $error['message'] .
            ' | FILE: ' . $error['file'] .
            ' | LINE: ' . $error['line']
        );
    }
});

error_log('CAKRAWALA_START: PHP=' . PHP_VERSION);

require __DIR__ . '/../public/index.php';