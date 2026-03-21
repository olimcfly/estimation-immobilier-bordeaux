<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class NewsletterSubscriber
{
    public function confirmByEmail(string $email): void
    {
        $sql = 'INSERT INTO newsletter_subscribers (email, confirmed_at, created_at)
                VALUES (:email, NOW(), NOW())
                ON DUPLICATE KEY UPDATE confirmed_at = NOW()';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([
            ':email' => $email,
        ]);
    }
}
