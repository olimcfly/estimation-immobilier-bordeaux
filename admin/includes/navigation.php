<?php

declare(strict_types=1);

$adminMenu = [
    ['key' => 'dashboard', 'label' => 'Dashboard', 'href' => '/admin/index.php', 'icon' => '🏠'],
    ['key' => 'estimations', 'label' => 'Estimations', 'href' => '/admin/lead.php', 'icon' => '📊'],
    ['key' => 'leads', 'label' => 'Leads', 'href' => '/admin/leads/index.php', 'icon' => '👥'],
    [
        'key' => 'google-ads',
        'label' => 'Google Ads',
        'href' => '/admin/google-ads/index.php',
        'icon' => '📈',
        'children' => [
            ['label' => 'Stratégies', 'href' => '/admin/google-ads/index.php'],
            ['label' => 'Campagnes', 'href' => '/admin/google-ads/campaigns.php'],
        ],
    ],
    ['key' => 'traffic', 'label' => 'Trafic & Publicité', 'href' => '/admin/traffic/index.php', 'icon' => '📢'],
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
    ['key' => 'webhooks', 'label' => 'Webhooks', 'href' => '/admin/webhooks.php', 'icon' => '🪝'],
    ['key' => 'exports', 'label' => 'Exports', 'href' => '/admin/settings.php#backup', 'icon' => '📤'],
];

$adminTopNav = [
    ['key' => 'settings', 'label' => 'Paramètres', 'href' => '/admin/settings.php'],
    ['key' => 'theme', 'label' => 'Mode sombre', 'href' => '#', 'isToggle' => true],
    ['key' => 'logout', 'label' => 'Déconnexion', 'href' => '/admin/logout.php'],
];
