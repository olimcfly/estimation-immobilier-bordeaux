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

        $pageContent = self::renderTemplate($templatePath, $data);
        $data['meta_description'] = self::resolveMetaDescription($data, $pageContent);

        $siteConfig = getSiteConfig();
        $data['colors'] = $siteConfig['colors'] ?? [];
        $data['rgbColors'] = $siteConfig['rgb_colors'] ?? [];

        extract($data, EXTR_SKIP);
        include __DIR__ . '/../views/layouts/header.php';
        echo $pageContent;
        include __DIR__ . '/../views/layouts/footer.php';
    }

    /**
     * Render a template inside the admin layout with sidebar navigation.
     */
    public static function renderAdmin(string $template, array $data = []): void
    {
        $templatePath = __DIR__ . '/../views/' . $template . '.php';

        if (!is_file($templatePath)) {
            http_response_code(500);
            echo 'Template not found.';
            return;
        }

        $pageContent = self::renderTemplate($templatePath, $data);

        $layoutPath = __DIR__ . '/../views/layouts/admin.php';
        ob_start();
        extract($data, EXTR_SKIP);
        include $layoutPath;
        $layoutHtml = (string) ob_get_clean();

        echo str_replace('%%ADMIN_CONTENT%%', $pageContent, $layoutHtml);
    }

    /**
     * Render a template without the site header and footer (bare layout).
     */
    public static function renderBare(string $template, array $data = []): void
    {
        $templatePath = __DIR__ . '/../views/' . $template . '.php';

        if (!is_file($templatePath)) {
            http_response_code(500);
            echo 'Template not found.';
            return;
        }

        extract($data, EXTR_SKIP);
        include $templatePath;
    }

    private static function renderTemplate(string $templatePath, array $data): string
    {
        ob_start();
        extract($data, EXTR_SKIP);
        include $templatePath;
        return (string) ob_get_clean();
    }

    private static function resolveMetaDescription(array $data, string $pageContent): string
    {
        $explicitDescription = trim((string) ($data['meta_description'] ?? ''));
        if ($explicitDescription !== '') {
            return $explicitDescription;
        }

        $pageTitle = trim((string) ($data['page_title'] ?? 'Estimation Immobilière Nandy'));
        $plainContent = html_entity_decode(strip_tags($pageContent), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $plainContent = preg_replace('/\s+/u', ' ', $plainContent) ?? '';
        $plainContent = trim($plainContent);
        $contentExcerpt = mb_substr($plainContent, 0, 160);

        if ($contentExcerpt === '') {
            return $pageTitle;
        }

        return $pageTitle . ' — ' . $contentExcerpt;
    }
}
