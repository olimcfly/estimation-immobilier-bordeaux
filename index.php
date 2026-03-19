<?php

declare(strict_types=1);

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
