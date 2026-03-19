<?php

declare(strict_types=1);

return [
    'app_name' => $_ENV['APP_NAME'] ?? 'Estimateur Immobilier SaaS',
    'base_url' => $_ENV['APP_BASE_URL'] ?? '',
    'db' => [
        'host' => $_ENV['DB_HOST'] ?? '127.0.0.1',
        'port' => (int) ($_ENV['DB_PORT'] ?? 3306),
        'name' => $_ENV['DB_NAME'] ?? 'immobilier_saas',
        'user' => $_ENV['DB_USER'] ?? 'root',
        'pass' => $_ENV['DB_PASS'] ?? '',
        'charset' => $_ENV['DB_CHARSET'] ?? 'utf8mb4',
    ],
    'perplexity' => [
        'api_key' => $_ENV['PERPLEXITY_API_KEY'] ?? '',
        'model' => $_ENV['PERPLEXITY_MODEL'] ?? 'sonar-pro',
        'endpoint' => $_ENV['PERPLEXITY_ENDPOINT'] ?? 'https://api.perplexity.ai/chat/completions',
    ],
    'openai' => [
        'api_key' => $_ENV['OPENAI_API_KEY'] ?? '',
        'model' => $_ENV['OPENAI_MODEL'] ?? 'gpt-4o-mini',
        'endpoint' => $_ENV['OPENAI_ENDPOINT'] ?? 'https://api.openai.com/v1/chat/completions',
    ],
];
