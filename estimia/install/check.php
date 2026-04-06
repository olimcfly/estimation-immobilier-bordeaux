<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

$rootDir = dirname(__DIR__);
$configDir = $rootDir . '/config';

$checks = [
    'php_version' => [
        'required' => '8.0',
        'current' => PHP_VERSION,
        'ok' => version_compare(PHP_VERSION, '8.0.0', '>='),
    ],
    'pdo_mysql' => ['ok' => extension_loaded('pdo_mysql')],
    'json' => ['ok' => extension_loaded('json')],
    'mbstring' => ['ok' => extension_loaded('mbstring')],
    'curl' => ['ok' => extension_loaded('curl')],
    'config_writable' => ['ok' => is_dir($configDir) && is_writable($configDir), 'path' => $configDir . '/'],
    'root_writable' => ['ok' => is_writable($rootDir)],
    'session' => ['ok' => function_exists('session_start')],
    'openssl' => ['ok' => extension_loaded('openssl')],
];

echo json_encode($checks, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
