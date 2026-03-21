<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$isAdminAuthenticated =
    (!empty($_SESSION['admin_authenticated']) && $_SESSION['admin_authenticated'] === true)
    || (!empty($_SESSION['is_admin']) && $_SESSION['is_admin'] === true)
    || (
        isset($_SESSION['user'])
        && is_array($_SESSION['user'])
        && (string) ($_SESSION['user']['role'] ?? '') === 'admin'
    );

if (!$isAdminAuthenticated) {
    http_response_code(401);

    echo json_encode([
        'success' => false,
        'error' => 'Authentification requise.',
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$pagesDirectory = dirname(__DIR__, 3) . '/app/views/pages';
$pages = [];

if (is_dir($pagesDirectory)) {
    foreach (glob($pagesDirectory . '/*.php') ?: [] as $filePath) {
        $pages[] = [
            'slug' => basename($filePath, '.php'),
            'template' => basename($filePath),
        ];
    }
}

sort($pages);

echo json_encode([
    'success' => true,
    'data' => $pages,
], JSON_UNESCAPED_UNICODE);
