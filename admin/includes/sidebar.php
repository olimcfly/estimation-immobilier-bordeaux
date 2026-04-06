<?php

declare(strict_types=1);

$currentPage = isset($currentPage) && is_string($currentPage) ? $currentPage : 'dashboard';

$menu = [
    ['key' => 'dashboard', 'label' => 'Dashboard', 'href' => '/admin/index.php', 'icon' => '🏠'],
    ['key' => 'estimations', 'label' => 'Estimations', 'href' => '/admin/lead.php', 'icon' => '📊'],
    ['key' => 'leads', 'label' => 'Leads', 'href' => '/admin/lead.php', 'icon' => '👥'],
    [
        'key' => 'settings',
        'label' => 'Paramètres',
        'href' => '/admin/settings.php',
        'icon' => '⚙️',
        'children' => [
            ['label' => 'Général', 'href' => '/admin/settings.php#general'],
            ['label' => 'Société', 'href' => '/admin/settings.php#company'],
            ['label' => 'Apparence', 'href' => '/admin/settings.php#appearance'],
            ['label' => 'Coefficients', 'href' => '/admin/settings.php#estimation'],
            ['label' => 'Emails & Relances', 'href' => '/admin/settings.php#emails'],
            ['label' => 'Notifications', 'href' => '/admin/settings.php#notifications'],
            ['label' => 'Intégrations', 'href' => '/admin/settings.php#integrations'],
            ['label' => 'Utilisateurs', 'href' => '/admin/settings.php#users'],
            ['label' => 'Sauvegarde', 'href' => '/admin/settings.php#backup'],
        ],
    ],
    [
        'key' => 'google-ads',
        'label' => 'Google Ads',
        'href' => '/admin/google-ads.php',
        'icon' => '📈',
        'children' => [
            ['label' => 'Vue d\'ensemble', 'href' => '/admin/google-ads.php#overview'],
            ['label' => 'Checklist', 'href' => '/admin/google-ads.php#checklist'],
            ['label' => 'Niveaux de conscience', 'href' => '/admin/google-ads.php#awareness'],
            ['label' => 'Mots-clés', 'href' => '/admin/google-ads.php#keywords'],
            ['label' => 'Annonces', 'href' => '/admin/google-ads.php#ads'],
            ['label' => 'Extensions', 'href' => '/admin/google-ads.php#extensions'],
            ['label' => 'Ciblage', 'href' => '/admin/google-ads.php#geo'],
            ['label' => 'Budget', 'href' => '/admin/google-ads.php#budget'],
            ['label' => 'Suivi conversions', 'href' => '/admin/google-ads.php#tracking'],
            ['label' => 'Optimisation', 'href' => '/admin/google-ads.php#optim'],
            ['label' => 'Export', 'href' => '/admin/google-ads.php#export'],
        ],
    ],
    ['key' => 'webhooks', 'label' => 'Webhooks', 'href' => '/admin/webhooks.php', 'icon' => '🪝'],
    ['key' => 'exports', 'label' => 'Exports', 'href' => '/admin/settings.php#backup', 'icon' => '📤'],
];
?>
<aside class="w-[250px] shrink-0 border-r border-slate-200 bg-white p-4">
    <nav class="space-y-1 text-sm">
        <?php foreach ($menu as $item): ?>
            <?php $isActive = $currentPage === $item['key']; ?>
            <div>
                <a href="<?= htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8') ?>" class="flex items-center gap-2 rounded-md px-3 py-2 font-medium transition <?= $isActive ? 'bg-blue-50 text-blue-700' : 'text-slate-700 hover:bg-slate-100' ?>">
                    <span><?= htmlspecialchars($item['icon'], ENT_QUOTES, 'UTF-8') ?></span>
                    <span><?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?></span>
                </a>
                <?php if (!empty($item['children']) && is_array($item['children'])): ?>
                    <div class="ml-7 mt-1 space-y-1 border-l border-slate-200 pl-3">
                        <?php foreach ($item['children'] as $child): ?>
                            <a href="<?= htmlspecialchars($child['href'], ENT_QUOTES, 'UTF-8') ?>" class="block rounded px-2 py-1 text-xs text-slate-500 hover:bg-slate-100 hover:text-slate-700">
                                <?= htmlspecialchars($child['label'], ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </nav>
</aside>
<main class="flex-1 p-6">
