<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/core/helpers.php';

spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/../app/';

    if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
        return;
    }

    $relativeClass = substr($class, strlen($prefix));
    $parts = explode('\\', $relativeClass);
    $className = array_pop($parts);
    $dir = strtolower(implode('/', $parts));
    $file = $baseDir . ($dir !== '' ? $dir . '/' : '') . $className . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});
