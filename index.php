<?php

declare(strict_types=1);

$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (($_SERVER['SERVER_PORT'] ?? null) === '443')
    || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');

$host = $_SERVER['HTTP_HOST'] ?? '';
$isLocalHost = str_starts_with($host, 'localhost') || str_starts_with($host, '127.0.0.1');

if (PHP_SAPI !== 'cli' && !$isHttps && !$isLocalHost) {
    $target = 'https://' . $host . ($_SERVER['REQUEST_URI'] ?? '/');
    header('Location: ' . $target, true, 301);
    exit;
}

if (PHP_SAPI !== 'cli') {
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: geolocation=(), microphone=(), camera=()');

    $csp = implode('; ', [
        "default-src 'self'",
        "base-uri 'self'",
        "frame-ancestors 'none'",
        "form-action 'self'",
        "script-src 'self' 'unsafe-inline'",
        "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com",
        "font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com data:",
        "img-src 'self' data: https:",
        "connect-src 'self'",
        "upgrade-insecure-requests",
    ]);

    header('Content-Security-Policy: ' . $csp);
}

if (version_compare(PHP_VERSION, '8.1.0', '<')) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Configuration PHP invalide : cette application nécessite PHP 8.1+ (version détectée : ' . PHP_VERSION . ').';
    exit;
}

use App\Core\Router;

require_once __DIR__ . '/app/core/bootstrap.php';

$router = new Router();
require __DIR__ . '/routes/web.php';

$router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI'] ?? '/');
