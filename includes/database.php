<?php

class Database
{
    public static function getConnection(): PDO
    {
        static $pdo = null;
        if ($pdo instanceof PDO) {
            return $pdo;
        }

        $dsn = getenv('DB_DSN') ?: 'sqlite:' . __DIR__ . '/../sql/estimia.sqlite';
        $user = getenv('DB_USER') ?: null;
        $pass = getenv('DB_PASS') ?: null;

        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        return $pdo;
    }
}
