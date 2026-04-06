<?php

declare(strict_types=1);

if (file_exists(__DIR__ . '/config/config.php')) {
    require_once __DIR__ . '/config/config.php';
}

if (!file_exists(__DIR__ . '/config/config.php') || !defined('SITE_NAME')) {
    header('Location: /install/');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= defined('HOME_H1') ? htmlspecialchars(HOME_H1, ENT_QUOTES, 'UTF-8') : htmlspecialchars(SITE_NAME, ENT_QUOTES, 'UTF-8') ?></title>
    <meta name="description" content="<?= defined('HOME_META_DESC') ? htmlspecialchars(HOME_META_DESC, ENT_QUOTES, 'UTF-8') : '' ?>">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white text-slate-900 antialiased">
<?php require __DIR__ . '/templates/home.php'; ?>
</body>
</html>
