<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (empty($_SESSION['admin_logged'])) {
    redirect('login.php');
}

$adminPageTitle = isset($adminPageTitle) && is_string($adminPageTitle) && $adminPageTitle !== ''
    ? $adminPageTitle
    : 'Dashboard';

$currentFile = basename((string) ($_SERVER['PHP_SELF'] ?? ''));
$adminCsrfToken = generateCSRFToken();

$navItems = [
    ['label' => 'Dashboard', 'href' => 'dashboard.php', 'icon' => 'layout-dashboard', 'active' => in_array($currentFile, ['index.php', 'dashboard.php'], true)],
    ['label' => 'Analytiques', 'href' => 'analytics.php', 'icon' => 'bar-chart-3', 'active' => $currentFile === 'analytics.php'],
    ['label' => 'Estimations', 'href' => 'estimations.php', 'icon' => 'calculator', 'active' => $currentFile === 'estimations.php'],
    ['label' => 'Pipeline', 'href' => 'pipeline.php', 'icon' => 'columns-3', 'active' => $currentFile === 'pipeline.php'],
    ['label' => 'Gestion RDV', 'href' => 'rdv-management.php', 'icon' => 'calendar-range', 'active' => $currentFile === 'rdv-management.php'],
    ['label' => 'Prospection', 'href' => 'carte-prospection.php', 'icon' => 'map', 'active' => $currentFile === 'carte-prospection.php'],
    ['label' => 'RDV', 'href' => 'estimations.php?rdv=1', 'icon' => 'calendar-check', 'active' => isset($_GET['rdv'])],
    ['label' => 'Déconnexion', 'href' => 'logout.php', 'icon' => 'log-out', 'active' => false],
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo htmlspecialchars($adminCsrfToken, ENT_QUOTES, 'UTF-8'); ?>">
    <title><?php echo htmlspecialchars($adminPageTitle, ENT_QUOTES, 'UTF-8'); ?> | Admin EstimIA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-gray-100 font-sans text-gray-900">
<aside class="fixed inset-y-0 left-0 z-40 w-64 bg-gray-900 p-6 text-white">
    <a href="dashboard.php" class="mb-10 block text-2xl font-extrabold">EstimIA</a>

    <nav class="space-y-2">
        <?php foreach ($navItems as $item): ?>
            <a href="<?php echo $item['href']; ?>"
               class="flex items-center gap-3 rounded-lg px-4 py-2.5 text-sm transition <?php echo $item['active'] ? 'bg-white/10' : 'hover:bg-white/5'; ?>">
                <i data-lucide="<?php echo $item['icon']; ?>" class="h-4 w-4"></i>
                <span><?php echo $item['label']; ?></span>
            </a>
        <?php endforeach; ?>
    </nav>
</aside>

<div class="ml-64 min-h-screen">
    <script>
        window.adminCsrfToken = <?php echo json_encode($adminCsrfToken, JSON_UNESCAPED_UNICODE); ?>;
    </script>
    <header class="border-b border-gray-200 bg-white px-8 py-5">
        <h1 class="text-2xl font-bold"><?php echo htmlspecialchars($adminPageTitle, ENT_QUOTES, 'UTF-8'); ?></h1>
    </header>
    <main class="p-8">
