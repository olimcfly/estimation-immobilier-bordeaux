<?php

declare(strict_types=1);

use App\Core\Config;
use App\Core\Database;

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function base_path(string $path = ''): string
{
    $base = dirname(__DIR__, 2);
    return $path === '' ? $base : $base . '/' . ltrim($path, '/');
}

function hex_to_rgb(string $hex): string
{
    $value = ltrim(trim($hex), '#');

    if (strlen($value) === 3) {
        $value = preg_replace('/(.)/', '$1$1', $value) ?? $value;
    }

    if (!preg_match('/^[a-fA-F0-9]{6}$/', $value)) {
        return '0, 0, 0';
    }

    return sprintf(
        '%d, %d, %d',
        (int) hexdec(substr($value, 0, 2)),
        (int) hexdec(substr($value, 2, 2)),
        (int) hexdec(substr($value, 4, 2))
    );
}

function getSiteConfig(): array
{
    $defaultColors = (array) Config::get('site.colors', []);
    $colors = $defaultColors;

    try {
        $statement = Database::connection()->query("SELECT `key`, `value` FROM settings WHERE `key` LIKE 'site.colors.%'");
        $rows = $statement !== false ? $statement->fetchAll() : [];

        foreach ($rows as $row) {
            $colorKey = str_replace('site.colors.', '', (string) ($row['key'] ?? ''));
            $colorValue = trim((string) ($row['value'] ?? ''));

            if ($colorKey !== '' && $colorValue !== '') {
                $colors[$colorKey] = $colorValue;
            }
        }
    } catch (Throwable) {
        // Table/settings can be unavailable in some environments.
    }

    $rgbColors = [];

    foreach ($colors as $name => $hexColor) {
        if (is_string($hexColor)) {
            $rgbColors[$name] = hex_to_rgb($hexColor);
        }
    }

    return [
        'colors' => $colors,
        'rgb_colors' => $rgbColors,
    ];
}
