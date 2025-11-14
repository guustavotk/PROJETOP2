<?php


if (!defined('APP_ROOT')) {
    define('APP_ROOT', dirname(__DIR__));
}

if (!is_dir(APP_ROOT . '/logs')) {
    mkdir(APP_ROOT . '/logs', 0755, true);
}

function logError(string $message, string $file = 'error_log.txt'): void
{
    $logFile = APP_ROOT . '/logs/' . $file;
    $entry = sprintf(
        "[%s] %s\n",
        date('Y-m-d H:i:s'),
        $message
    );
    error_log($entry, 3, $logFile);
}

set_exception_handler(function (Throwable $e) {
    logError("[EXCEPTION] " . $e->getMessage() . " in " . $e->getFile() . " line " . $e->getLine());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => '⚠️ Ocorreu um erro interno no servidor. Por favor, tente novamente mais tarde.'
    ], JSON_UNESCAPED_UNICODE);
});


set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    $msg = "[ERROR] ($errno) $errstr in $errfile line $errline";
    logError($msg);
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => '⚠️ Ocorreu um erro interno inesperado.'
    ], JSON_UNESCAPED_UNICODE);
    return true; 
});


register_shutdown_function(function () {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE])) {
        logError("[FATAL] {$error['message']} in {$error['file']} line {$error['line']}");
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => '🚨 Ocorreu uma falha crítica no servidor.'
        ], JSON_UNESCAPED_UNICODE);
    }
});
