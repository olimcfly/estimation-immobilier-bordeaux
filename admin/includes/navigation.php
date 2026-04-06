<?php

declare(strict_types=1);

$moduleState = [
    'google-ads' => true,
    'traffic' => true,
    'webhooks' => true,
    'leads' => true,
    'users' => true,
];

try {
    $db = Database::getConnection();
    $db->exec(
        "CREATE TABLE IF NOT EXISTS modules (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL UNIQUE,
            description TEXT NULL,
            is_active TINYINT(1) DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    );

    $seedModules = [
        ['Google Ads', 'Gestion des campagnes Google Ads'],
        ['Webhooks', 'Intégration des webhooks'],
        ['Leads', 'Gestion des leads et estimations'],
        ['Traffic', 'Analyse du trafic'],
        ['Users', 'Gestion des comptes administrateurs'],
    ];

    $insertStmt = $db->prepare('INSERT IGNORE INTO modules (name, description, is_active) VALUES (:name, :description, 1)');
    foreach ($seedModules as $module) {
        $insertStmt->execute(['name' => $module[0], 'description' => $module[1]]);
    }

    $rows = $db->query('SELECT name, is_active FROM modules')->fetchAll(PDO::FETCH_ASSOC);
    $map = [
        'Google Ads' => 'google-ads',
        'Webhooks' => 'webhooks',
        'Leads' => 'leads',
        'Traffic' => 'traffic',
        'Users' => 'users',
    ];
    foreach ($rows as $row) {
        $key = $map[(string) ($row['name'] ?? '')] ?? null;
        if ($key !== null) {
            $moduleState[$key] = (int) $row['is_active'] === 1;
        }
    }
} catch (Throwable $exception) {
    // fallback: keep default full menu
}

$adminMenu = [
    ['key' => 'dashboard', 'label' => 'Dashboard', 'href' => '/admin/index.php', 'icon' => '🏠'],
];

if ($moduleState['leads']) {
    $adminMenu[] = ['key' => 'estimations', 'label' => 'Leads CRM', 'href' => '/admin/lead.php', 'icon' => '📊'];
}

if ($moduleState['google-ads']) {
    $adminMenu[] = [
        'key' => 'google-ads',
        'label' => 'Google Ads',
        'href' => '/admin/google-ads/index.php',
        'icon' => '📈',
        'children' => [
            ['label' => 'Stratégies', 'href' => '/admin/google-ads/index.php'],
            ['label' => 'Campagnes', 'href' => '/admin/google-ads/campaigns.php'],
            ['label' => 'Leads Ads', 'href' => '/admin/leads/index.php'],
        ],
    ];
}

if ($moduleState['traffic']) {
    $adminMenu[] = ['key' => 'traffic', 'label' => 'Trafic & Publicité', 'href' => '/admin/traffic/index.php', 'icon' => '📢'];
}

$adminMenu[] = [
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
];

if ($moduleState['webhooks']) {
    $adminMenu[] = ['key' => 'webhooks', 'label' => 'Webhooks', 'href' => '/admin/webhooks.php', 'icon' => '🪝'];
}

if ($moduleState['users']) {
    $adminMenu[] = ['key' => 'users', 'label' => 'Utilisateurs', 'href' => '/admin/users.php', 'icon' => '👥'];
}

$adminMenu[] = ['key' => 'exports', 'label' => 'Exports', 'href' => '/admin/settings.php#backup', 'icon' => '📤'];

$adminTopNav = [
    ['key' => 'settings', 'label' => 'Paramètres', 'href' => '/admin/settings.php'],
    ['key' => 'theme', 'label' => 'Mode sombre', 'href' => '#', 'isToggle' => true],
    ['key' => 'logout', 'label' => 'Déconnexion', 'href' => '/admin/logout.php'],
];
