<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/database.php';
require_once __DIR__ . '/../../includes/security.php';
require_once __DIR__ . '/../../includes/admin-auth.php';
require_once __DIR__ . '/navigation.php';

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
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= admin_h($pageTitle) ?> · <?= admin_h(SITE_NAME) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root,
        [data-theme="light"] {
            --admin-sidebar-bg: #0d223d;
            --admin-sidebar-text: #f8fafc;
            --admin-sidebar-muted: #aac0de;
            --admin-sidebar-active-bg: #173a63;
            --admin-content-bg: #f8fafc;
            --admin-header-bg: #ffffff;
            --admin-header-text: #0f172a;
            --admin-border: #e2e8f0;
            --admin-sidebar-accent: #ff7a1a;
            --admin-sidebar-hover: #102d51;
        }

        [data-theme="dark"] {
            --admin-sidebar-bg: #081a31;
            --admin-sidebar-text: #e2e8f0;
            --admin-sidebar-muted: #92abd0;
            --admin-sidebar-active-bg: #133559;
            --admin-content-bg: #0f172a;
            --admin-header-bg: #0b1220;
            --admin-header-text: #f8fafc;
            --admin-border: #334155;
            --admin-sidebar-accent: #ff8f3d;
            --admin-sidebar-hover: #102846;
        }

        .focus-visible-ring:focus-visible {
            outline: 3px solid #facc15;
            outline-offset: 2px;
        }
    </style>
</head>
<body class="bg-[var(--admin-content-bg)] text-slate-900 transition-colors duration-200" data-theme="light">
<header class="fixed inset-x-0 top-0 z-50 h-16 border-b bg-[var(--admin-header-bg)] text-[var(--admin-header-text)]" style="border-color: var(--admin-border)">
    <div class="mx-auto flex h-full max-w-[1600px] items-center justify-between px-6">
        <div class="flex items-center gap-3">
            <button type="button" id="sidebar-toggle" class="focus-visible-ring inline-flex items-center justify-center rounded-md border p-2 text-inherit transition hover:bg-slate-100/20 lg:hidden" style="border-color: var(--admin-border)" aria-controls="admin-sidebar" aria-expanded="false" aria-label="Ouvrir le menu latéral">
                ☰
            </button>
            <a href="/admin/index.php" class="focus-visible-ring text-xl font-bold tracking-tight">EstimIA</a>
        </div>
        <nav class="flex items-center gap-1 text-sm font-medium">
            <?php foreach ($adminTopNav as $item): ?>
                <?php if (!empty($item['isToggle'])): ?>
                    <button type="button" id="theme-toggle" class="focus-visible-ring rounded-md px-3 py-2 transition hover:bg-slate-100/20" aria-pressed="false">
                        <?= admin_h($item['label']) ?>
                    </button>
                <?php else: ?>
                    <a href="<?= admin_h($item['href']) ?>" class="focus-visible-ring rounded-md px-3 py-2 transition hover:bg-slate-100/20">
                        <?= admin_h($item['label']) ?>
                    </a>
                <?php endif; ?>
            <?php endforeach; ?>
        </nav>
    </div>
</header>
<div id="sidebar-overlay" class="fixed inset-0 z-30 hidden bg-slate-950/50 lg:hidden"></div>
<div class="mx-auto flex min-h-screen max-w-[1600px] pt-16">
