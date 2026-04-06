<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/database.php';

$debugEnabled = defined('DEBUG_MODE') && DEBUG_MODE === true;

if (!$debugEnabled) {
    http_response_code(403);
    echo 'Mode debug désactivé (DEBUG_MODE=false).';
    exit;
}

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

echo '<h1>Diagnostic base de données</h1>';

try {
    $db = Database::getConnection();
    $tables = $db->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
    echo '<p>✅ Connexion à la base de données réussie.</p>';
    echo '<p>Tables détectées : ' . count($tables) . '</p>';
} catch (Throwable $e) {
    echo '<p>❌ Erreur de connexion : ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</p>';
}
