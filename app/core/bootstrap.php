<?php

declare(strict_types=1);

use App\Core\Config;

require_once __DIR__ . '/helpers.php';

spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relative = str_replace('\\', '/', substr($class, strlen($prefix)));
    $segments = explode('/', $relative);

    if (isset($segments[0])) {
        $segments[0] = strtolower($segments[0]);
    }

    $file = __DIR__ . '/../' . implode('/', $segments) . '.php';

    if (is_file($file)) {
        require_once $file;
    }
});


if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

Config::load(base_path('config/config.php'));
