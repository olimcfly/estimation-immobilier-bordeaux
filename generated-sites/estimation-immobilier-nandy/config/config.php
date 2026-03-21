<?php

declare(strict_types=1);

if (!defined('CITY_NAME')) {
    define('CITY_NAME', 'Nandy');
}
if (!defined('CITY_REGION')) {
    define('CITY_REGION', 'Île-de-France');
}
if (!defined('CITY_CODE_POSTAL')) {
    define('CITY_CODE_POSTAL', '77176');
}
if (!defined('QUARTIERS')) {
    define('QUARTIERS', ['Chartrons', 'Saint-Pierre', 'Saint-Michel', 'Caudéran', 'Bastide', 'Mériadeck']);
}
if (!defined('PRIX_M2_MOYEN')) {
    define('PRIX_M2_MOYEN', 3200);
}
if (!defined('COLOR_PRIMARY')) {
    define('COLOR_PRIMARY', '#2E7D32');
}
if (!defined('COLOR_SECONDARY')) {
    define('COLOR_SECONDARY', '#FFFFFF');
}
if (!defined('COLOR_ACCENT')) {
    define('COLOR_ACCENT', '#FF8F00');
}

return [
    'app_name' => $_ENV['APP_NAME'] ?? 'Estimateur Immobilier SaaS',
    'base_url' => $_ENV['APP_BASE_URL'] ?? '',
    'website' => [
        'id' => (int) ($_ENV['WEBSITE_ID'] ?? 1),
    ],
    'db' => [
        'host' => $_ENV['DB_HOST'] ?? '127.0.0.1',
        'port' => (int) ($_ENV['DB_PORT'] ?? 3306),
        'name' => $_ENV['DB_NAME'] ?? 'immobilier_nandy',
        'user' => $_ENV['DB_USER'] ?? 'root',
        'pass' => $_ENV['DB_PASS'] ?? '',
        'charset' => $_ENV['DB_CHARSET'] ?? 'utf8mb4',
    ],
    'perplexity' => [
        'api_key' => $_ENV['PERPLEXITY_API_KEY'] ?? '',
        'model' => $_ENV['PERPLEXITY_MODEL'] ?? 'sonar-pro',
        'endpoint' => $_ENV['PERPLEXITY_ENDPOINT'] ?? 'https://api.perplexity.ai/chat/completions',
    ],
    'mail' => [
        'from' => $_ENV['MAIL_FROM_ADDRESS'] ?? $_ENV['MAIL_FROM'] ?? 'contact@estimation-immobilier-nandy.fr',
        'from_name' => $_ENV['MAIL_FROM_NAME'] ?? 'Estimation Immobilier Nandy',
        'admin_email' => $_ENV['MAIL_ADMIN_EMAIL'] ?? $_ENV['MAIL_FROM_ADDRESS'] ?? $_ENV['MAIL_FROM'] ?? 'contact@estimation-immobilier-nandy.fr',
        'smtp_host' => $_ENV['MAIL_SMTP_HOST'] ?? $_ENV['MAIL_HOST'] ?? '',
        'smtp_port' => (int) ($_ENV['MAIL_SMTP_PORT'] ?? $_ENV['MAIL_PORT'] ?? 587),
        'smtp_user' => $_ENV['MAIL_SMTP_USER'] ?? $_ENV['MAIL_USERNAME'] ?? '',
        'smtp_pass' => $_ENV['MAIL_SMTP_PASS'] ?? $_ENV['MAIL_PASSWORD'] ?? '',
        'smtp_encryption' => $_ENV['MAIL_SMTP_ENCRYPTION'] ?? $_ENV['MAIL_ENCRYPTION'] ?? 'tls',
    ],
    'openai' => [
        'api_key' => $_ENV['OPENAI_API_KEY'] ?? '',
        'model' => $_ENV['OPENAI_MODEL'] ?? 'gpt-4o-mini',
        'endpoint' => $_ENV['OPENAI_ENDPOINT'] ?? 'https://api.openai.com/v1/chat/completions',
    ],
    'anthropic' => [
        'api_key' => $_ENV['ANTHROPIC_API_KEY'] ?? '',
        'model' => $_ENV['ANTHROPIC_MODEL'] ?? 'claude-sonnet-4-20250514',
    ],
    'google_maps' => [
        'api_key' => $_ENV['GOOGLE_MAPS_API_KEY'] ?? '',
    ],
    'sms_partner' => [
        'api_key' => $_ENV['SMSPARTNER_API_KEY'] ?? '',
    ],
    'twilio' => [
        'account_sid' => $_ENV['TWILIO_ACCOUNT_SID'] ?? '',
        'auth_token' => $_ENV['TWILIO_AUTH_TOKEN'] ?? '',
        'phone_number' => $_ENV['TWILIO_PHONE_NUMBER'] ?? '',
    ],
    'city' => [
        'name' => CITY_NAME,
        'region' => CITY_REGION,
        'code_postal' => CITY_CODE_POSTAL,
        'quartiers' => QUARTIERS,
        'prix_m2_moyen' => PRIX_M2_MOYEN,
        'colors' => [
            'primary' => COLOR_PRIMARY,
            'secondary' => COLOR_SECONDARY,
            'accent' => COLOR_ACCENT,
        ],
    ],
    'site' => [
        'colors' => [
            'bg' => $_ENV['SITE_COLOR_BG'] ?? '#faf9f7',
            'surface' => $_ENV['SITE_COLOR_SURFACE'] ?? '#ffffff',
            'text' => $_ENV['SITE_COLOR_TEXT'] ?? '#1a1410',
            'muted' => $_ENV['SITE_COLOR_MUTED'] ?? '#6b6459',
            'primary' => $_ENV['SITE_COLOR_PRIMARY'] ?? '#8B1538',
            'primary_dark' => $_ENV['SITE_COLOR_PRIMARY_DARK'] ?? '#6b0f2d',
            'accent' => $_ENV['SITE_COLOR_ACCENT'] ?? '#D4AF37',
            'accent_light' => $_ENV['SITE_COLOR_ACCENT_LIGHT'] ?? '#E8C547',
            'border' => $_ENV['SITE_COLOR_BORDER'] ?? '#e8dfd7',
            'success' => $_ENV['SITE_COLOR_SUCCESS'] ?? '#22c55e',
            'warning' => $_ENV['SITE_COLOR_WARNING'] ?? '#f97316',
            'danger' => $_ENV['SITE_COLOR_DANGER'] ?? '#e24b4a',
            'info' => $_ENV['SITE_COLOR_INFO'] ?? '#3b82f6',
            'neutral' => $_ENV['SITE_COLOR_NEUTRAL'] ?? '#000000',
        ],
    ],
    'maintenance' => [
        'enabled' => filter_var($_ENV['MAINTENANCE_MODE'] ?? false, FILTER_VALIDATE_BOOLEAN),
        'retry_after' => (int) ($_ENV['MAINTENANCE_RETRY_AFTER'] ?? 3600),
        'allowed_paths' => [
            '/admin/login',
            '/admin/logout',
            '/admin/leads',
        ],
    ],
];
