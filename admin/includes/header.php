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
    'dashboard' => ['label' => 'Dashboard', 'href' => '/admin/index.php'],
    'estimations' => ['label' => 'Estimations', 'href' => '/admin/lead.php'],
    'leads' => ['label' => 'Leads', 'href' => '/admin/lead.php'],
    'settings' => ['label' => 'Paramètres', 'href' => '/admin/settings.php'],
    'google-ads' => ['label' => 'Google Ads', 'href' => '/admin/google-ads.php'],
    'exports' => ['label' => 'Exports', 'href' => '/admin/settings.php#backup'],
];

$topNavCurrent = isset($topNavCurrent) && is_string($topNavCurrent) ? $topNavCurrent : 'dashboard';
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= admin_h($pageTitle) ?> · <?= admin_h(SITE_NAME) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#f9fafb] text-slate-900">
<header class="fixed inset-x-0 top-0 z-50 h-16 border-b border-slate-200 bg-white">
    <div class="mx-auto flex h-full max-w-[1600px] items-center justify-between px-6">
        <a href="/admin/index.php" class="text-xl font-bold tracking-tight text-slate-900">EstimIA</a>
        <nav class="flex items-center gap-1 text-sm font-medium text-slate-600">
            <?php foreach ($topNav as $key => $item): ?>
                <a href="<?= admin_h($item['href']) ?>" class="rounded-md px-3 py-2 transition <?= $topNavCurrent === $key ? 'bg-slate-100 text-slate-900' : 'hover:bg-slate-100 hover:text-slate-900' ?>">
                    <?= admin_h($item['label']) ?>
                </a>
            <?php endforeach; ?>
        </nav>
    </div>
</header>
<div class="mx-auto flex min-h-screen max-w-[1600px] pt-16">
