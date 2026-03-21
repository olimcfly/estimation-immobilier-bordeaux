<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

final class AdminUser
{
    private const CODE_TTL_MINUTES = 10;

    public static function findByEmail(string $email): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM admin_users WHERE email = :email LIMIT 1'
        );
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row !== false ? $row : null;
    }

    public static function storeLoginCode(string $email, string $code): void
    {
        $expiresAt = date('Y-m-d H:i:s', time() + self::CODE_TTL_MINUTES * 60);

        $stmt = Database::connection()->prepare(
            'UPDATE admin_users SET login_code = :code, login_code_expires_at = :expires WHERE email = :email'
        );
        $stmt->execute([
            'code' => password_hash($code, PASSWORD_BCRYPT, ['cost' => 10]),
            'expires' => $expiresAt,
            'email' => $email,
        ]);
    }

    public static function verifyLoginCode(string $email, string $code): bool
    {
        $user = self::findByEmail($email);
        if ($user === null) {
            return false;
        }

        $hash = (string) ($user['login_code'] ?? '');
        $expiresAt = (string) ($user['login_code_expires_at'] ?? '');

        if ($hash === '' || $expiresAt === '') {
            return false;
        }

        if (strtotime($expiresAt) < time()) {
            return false;
        }

        return password_verify($code, $hash);
    }

    public static function clearLoginCode(string $email): void
    {
        $stmt = Database::connection()->prepare(
            'UPDATE admin_users SET login_code = NULL, login_code_expires_at = NULL WHERE email = :email'
        );
        $stmt->execute(['email' => $email]);
    }

    public static function generateCode(): string
    {
        return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    public static function createTable(): void
    {
        Database::connection()->exec("
            CREATE TABLE IF NOT EXISTS admin_users (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                email VARCHAR(180) NOT NULL UNIQUE,
                name VARCHAR(120) NOT NULL DEFAULT '',
                login_code VARCHAR(255) DEFAULT NULL,
                login_code_expires_at DATETIME DEFAULT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_admin_email (email)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    public static function seedDefaultAdmin(string $email): void
    {
        $existing = self::findByEmail($email);
        if ($existing !== null) {
            return;
        }

        $stmt = Database::connection()->prepare(
            'INSERT INTO admin_users (email, name, created_at) VALUES (:email, :name, NOW())'
        );
        $stmt->execute([
            'email' => $email,
            'name' => 'Administrateur',
        ]);
    }
}
