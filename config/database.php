<?php

declare(strict_types=1);

use App\Core\Config;

final class Database
{
    private static ?PDO $instance = null;

    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            $host = (string) Config::get('db.host');
            $port = (int) Config::get('db.port');
            $dbname = (string) Config::get('db.name');
            $username = (string) Config::get('db.user');
            $password = (string) Config::get('db.pass');

            self::$instance = new PDO(
                "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4",
                $username,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        }

        return self::$instance;
    }
}
