<?php

declare(strict_types=1);

use App\Core\Config;

require_once __DIR__ . '/helpers.php';

// Charger le fichier .env dans $_ENV
$envFile = dirname(__DIR__, 2) . '/.env';
if (is_file($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        if (str_contains($line, '=')) {
            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            // Retirer les guillemets entourant la valeur
            if (strlen($value) >= 2 && $value[0] === '"' && $value[-1] === '"') {
                $value = substr($value, 1, -1);
            }
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }
}

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
