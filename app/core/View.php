<?php

declare(strict_types=1);

namespace App\Core;

final class View
{
    public static function render(string $template, array $data = []): void
    {
        $templatePath = __DIR__ . '/../views/' . $template . '.php';

        if (!is_file($templatePath)) {
            http_response_code(500);
            echo 'Template not found.';
            return;
        }

        $data['config'] = $data['config'] ?? getSiteConfig();

        extract($data, EXTR_SKIP);
        include __DIR__ . '/../views/layouts/header.php';
        include $templatePath;
        include __DIR__ . '/../views/layouts/footer.php';
    }
}
