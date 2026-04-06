<?php

declare(strict_types=1);

$db = Database::getConnection();
$modules = [];

try {
    $modules = $db->query(
        "SELECT * FROM `modules` WHERE `is_active` = TRUE ORDER BY `id`"
    )->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $exception) {
    $modules = [];
}

$adminMenu = array_map(
    static function (array $module): array {
        $slug = isset($module['slug']) ? trim((string) $module['slug']) : '';

        return [
            'key' => $slug !== '' ? $slug : 'module-' . (string) ($module['id'] ?? ''),
            'label' => isset($module['name']) ? (string) $module['name'] : 'Module',
            'href' => '/admin/' . ltrim($slug, '/'),
            'icon' => isset($module['icon']) ? (string) $module['icon'] : '🧩',
            'badge' => null,
        ];
    },
    $modules
);

if ($adminMenu === []) {
    $adminMenu = [
        ['key' => 'dashboard', 'label' => 'Dashboard', 'href' => '/admin/index.php', 'icon' => '🏠', 'badge' => null],
    ];
}

$adminResources = [
    ['label' => 'Centre d\'aide', 'href' => '/pages/faq.php'],
    ['label' => 'Playbook conversion', 'href' => '/site-specific/pages/ressources/index.php'],
    ['label' => 'Exporter les leads', 'href' => '/admin/export.php'],
];

$adminTopNav = [
    ['key' => 'theme', 'label' => 'Mode sombre', 'href' => '#', 'isToggle' => true],
    ['key' => 'logout', 'label' => 'Déconnexion', 'href' => '/admin/logout.php'],
];
