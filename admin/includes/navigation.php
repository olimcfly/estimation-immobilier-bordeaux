<?php

declare(strict_types=1);

$adminMenu = [
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

$adminTopNav = [
    ['key' => 'settings', 'label' => 'Paramètres', 'href' => '/admin/settings.php'],
    ['key' => 'theme', 'label' => 'Mode sombre', 'href' => '#', 'isToggle' => true],
    ['key' => 'logout', 'label' => 'Déconnexion', 'href' => '/admin/logout.php'],
];
