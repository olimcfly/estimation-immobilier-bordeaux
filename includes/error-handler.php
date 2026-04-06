<?php
/**
 * Gestionnaire d'erreurs global.
 * Inclus dans config.php après la définition des constantes.
 */

$logDir = __DIR__ . '/../logs';
if (!is_dir($logDir)) {
    mkdir($logDir, 0755, true);
}

$htaccessPath = $logDir . '/.htaccess';
if (!file_exists($htaccessPath)) {
    file_put_contents($htaccessPath, "Deny from all\n");
}

if (defined('DEBUG_MODE') && DEBUG_MODE === true) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', $logDir . '/php_errors.log');
}

set_error_handler(function (int $severity, string $message, string $file, int $line): bool {
    if (!(error_reporting() & $severity)) {
        return false;
    }

    $levelNames = [
        E_ERROR => 'ERROR',
        E_WARNING => 'WARNING',
        E_PARSE => 'PARSE',
        E_NOTICE => 'NOTICE',
        E_CORE_ERROR => 'CORE_ERROR',
        E_CORE_WARNING => 'CORE_WARNING',
        E_COMPILE_ERROR => 'COMPILE_ERROR',
        E_COMPILE_WARNING => 'COMPILE_WARNING',
        E_USER_ERROR => 'USER_ERROR',
        E_USER_WARNING => 'USER_WARNING',
        E_USER_NOTICE => 'USER_NOTICE',
        E_STRICT => 'STRICT',
        E_RECOVERABLE_ERROR => 'RECOVERABLE_ERROR',
        E_DEPRECATED => 'DEPRECATED',
        E_USER_DEPRECATED => 'USER_DEPRECATED',
    ];

    $level = $levelNames[$severity] ?? 'UNKNOWN';
    $logMessage = date('Y-m-d H:i:s') . " [$level] $message in $file:$line\n";

    file_put_contents(
        __DIR__ . '/../logs/app.log',
        $logMessage,
        FILE_APPEND | LOCK_EX
    );

    if ($severity === E_USER_ERROR) {
        http_response_code(500);
        include __DIR__ . '/../pages/500.php';
        exit;
    }

    return false;
});

set_exception_handler(function (Throwable $e): void {
    $logMessage = date('Y-m-d H:i:s') . ' [EXCEPTION] '
        . $e->getMessage()
        . ' in ' . $e->getFile() . ':' . $e->getLine()
        . "\nStack trace:\n" . $e->getTraceAsString() . "\n\n";

    file_put_contents(
        __DIR__ . '/../logs/app.log',
        $logMessage,
        FILE_APPEND | LOCK_EX
    );

    if (defined('DEBUG_MODE') && DEBUG_MODE === true) {
        echo '<h1>Exception</h1>';
        echo '<pre>' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</pre>';
        echo '<pre>' . htmlspecialchars($e->getTraceAsString(), ENT_QUOTES, 'UTF-8') . '</pre>';
    } else {
        http_response_code(500);
        include __DIR__ . '/../pages/500.php';
    }

    exit;
});

register_shutdown_function(function (): void {
    $lastError = error_get_last();
    if ($lastError === null) {
        return;
    }

    $fatalTypes = [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR];
    if (!in_array($lastError['type'], $fatalTypes, true)) {
        return;
    }

    $logMessage = date('Y-m-d H:i:s') . ' [FATAL] '
        . $lastError['message']
        . ' in ' . $lastError['file'] . ':' . $lastError['line'] . "\n";

    file_put_contents(
        __DIR__ . '/../logs/app.log',
        $logMessage,
        FILE_APPEND | LOCK_EX
    );

    if (!headers_sent()) {
        http_response_code(500);
    }

    if (!(defined('DEBUG_MODE') && DEBUG_MODE === true)) {
        include __DIR__ . '/../pages/500.php';
    }
});
