<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/security.php';

initSecureSession();
setSecurityHeaders();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars(SITE_NAME, ENT_QUOTES, 'UTF-8') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-900">
<header class="bg-white shadow-sm border-b border-gray-200">
    <div class="max-w-5xl mx-auto px-4 py-4 flex items-center justify-between">
        <a href="/" class="text-xl font-semibold text-primary"><?= htmlspecialchars(SITE_NAME, ENT_QUOTES, 'UTF-8') ?></a>
        <nav class="text-sm space-x-4">
            <a class="text-gray-600 hover:text-gray-900" href="/mentions-legales">Mentions légales</a>
            <a class="text-gray-600 hover:text-gray-900" href="/politique-confidentialite">Politique de confidentialité</a>
        </nav>
    </div>
</header>
<main class="max-w-5xl mx-auto px-4 py-8">
