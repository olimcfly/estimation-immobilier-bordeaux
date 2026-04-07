<?php

declare(strict_types=1);

final class Database
{
    private static ?PDO $instance = null;

    private function __construct()
    {
    }

    public static function getInstance(): PDO
    {
        if (self::$instance instanceof PDO) {
            return self::$instance;
        }

        $host = self::env('DB_HOST', defined('DB_HOST') ? (string) DB_HOST : 'localhost');
        $port = self::env('DB_PORT', '');
        $socket = self::env('DB_SOCKET', '');
        $dbName = self::env('DB_NAME', defined('DB_NAME') ? (string) DB_NAME : '');
        $user = self::env('DB_USER', defined('DB_USER') ? (string) DB_USER : '');
        $pass = self::env('DB_PASS', defined('DB_PASS') ? (string) DB_PASS : '');
        $charset = self::env('DB_CHARSET', 'utf8mb4');

        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=%s%s%s',
            $host,
            $dbName,
            $charset,
            $port !== '' ? ';port=' . (int) $port : '',
            $socket !== '' ? ';unix_socket=' . $socket : ''
        );

        self::$instance = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci',
        ]);

        return self::$instance;
    }

    public static function getConnection(): PDO
    {
        return self::getInstance();
    }

    public static function get(): PDO
    {
        return self::getInstance();
    }

    private static function env(string $key, string $default = ''): string
    {
        $value = $_ENV[$key] ?? getenv($key);
        if ($value === false || $value === null) {
            return $default;
        }

        return trim((string) $value);
    }
}
