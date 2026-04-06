<?php

declare(strict_types=1);

$configPath = __DIR__ . '/config.php';

if (!file_exists($configPath)) {
    $scriptName = (string) ($_SERVER['SCRIPT_NAME'] ?? '');
    $installPath = str_replace('/config', '/install', dirname($scriptName));
    header('Location: ' . rtrim($installPath, '/') . '/index.php');
    exit;
}

require_once $configPath;

class Database
{
    private static ?PDO $instance = null;

    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            try {
                $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $e) {
                if (defined('DEBUG_MODE') && DEBUG_MODE) {
                    die('Erreur DB : ' . $e->getMessage());
                }
                die('Erreur de connexion à la base de données.');
            }
        }

        return self::$instance;
    }
}
