<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/database.php';
require_once __DIR__ . '/../../includes/security.php';
require_once __DIR__ . '/../../includes/admin-auth.php';

initSecureSession();

if (!function_exists('admin_h')) {
    function admin_h(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!isset($pageTitle) || !is_string($pageTitle) || $pageTitle === '') {
    $pageTitle = 'Administration';
}

$topNav = [
    'settings' => ['label' => 'Paramètres', 'href' => '/admin/settings.php'],
    'logout' => ['label' => 'Déconnexion', 'href' => '/admin/logout.php'],
];
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= admin_h($pageTitle) ?> · <?= admin_h(SITE_NAME) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --admin-sidebar-bg: #111827;
            --admin-sidebar-text: #f8fafc;
            --admin-sidebar-muted: #cbd5e1;
            --admin-sidebar-active-bg: #1d4ed8;
            --admin-content-bg: #f8fafc;
        }
    </style>
</head>
<body class="bg-[var(--admin-content-bg)] text-slate-900">
<header class="fixed inset-x-0 top-0 z-50 h-16 border-b border-slate-200 bg-white">
    <div class="mx-auto flex h-full max-w-[1600px] items-center justify-between px-6">
        <div class="flex items-center gap-3">
            <button type="button" id="sidebar-toggle" class="inline-flex items-center justify-center rounded-md border border-slate-300 p-2 text-slate-700 transition hover:bg-slate-100 lg:hidden" aria-controls="admin-sidebar" aria-expanded="false" aria-label="Ouvrir le menu latéral">
                ☰
            </button>
            <a href="/admin/index.php" class="text-xl font-bold tracking-tight text-slate-900">EstimIA</a>
        </div>
        <nav class="flex items-center gap-1 text-sm font-medium text-slate-600">
            <?php foreach ($topNav as $key => $item): ?>
                <a href="<?= admin_h($item['href']) ?>" class="rounded-md px-3 py-2 transition hover:bg-slate-100 hover:text-slate-900">
                    <?= admin_h($item['label']) ?>
                </a>
            <?php endforeach; ?>
        </nav>
    </div>
</header>
<div id="sidebar-overlay" class="fixed inset-0 z-30 hidden bg-slate-950/50 lg:hidden"></div>
<div class="mx-auto flex min-h-screen max-w-[1600px] pt-16">
