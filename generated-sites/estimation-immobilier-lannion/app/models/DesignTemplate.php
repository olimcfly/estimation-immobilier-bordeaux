<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class DesignTemplate
{
    public function findBySlug(string $slug): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT id, slug
             FROM design_templates
             WHERE slug = :slug
             LIMIT 1'
        );
        $stmt->execute([':slug' => $slug]);

        $template = $stmt->fetch();

        return $template === false ? null : $template;
    }
}
