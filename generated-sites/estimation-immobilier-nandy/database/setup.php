<?php

/**
 * Comprehensive database setup script.
 *
 * Creates all required tables and seeds the default admin account.
 *
 * Usage: php database/setup.php [admin-email]
 */

declare(strict_types=1);

require_once __DIR__ . '/../app/core/bootstrap.php';

use App\Core\Config;
use App\Core\Database;
use App\Models\AdminUser;

$adminEmail = $argv[1] ?? $_ENV['ADMIN_EMAIL'] ?? 'contact@estimation-immobilier-nandy.fr';

echo "=== Initialisation de la base de données ===\n\n";

// 1. Test connection
echo "1. Connexion à la base de données... ";
try {
    $pdo = Database::connection();
    echo "OK\n";
} catch (\Throwable $e) {
    echo "ECHEC\n";
    echo "   Erreur : " . $e->getMessage() . "\n";
    echo "\n   Vérifiez votre fichier .env et la configuration de la base de données.\n";
    exit(1);
}

// 2. Run schema.sql
echo "\n2. Création des tables (schema.sql)...\n";
$schemaFile = __DIR__ . '/schema.sql';
if (!is_file($schemaFile)) {
    echo "   ERREUR : fichier schema.sql introuvable\n";
    exit(1);
}

$schema = file_get_contents($schemaFile);
$statements = array_filter(
    array_map('trim', explode(';', $schema)),
    static fn(string $s): bool => $s !== ''
);

foreach ($statements as $sql) {
    try {
        $pdo->exec($sql);
        // Extract table name from CREATE TABLE statement
        if (preg_match('/CREATE\s+TABLE\s+(?:IF\s+NOT\s+EXISTS\s+)?(\w+)/i', $sql, $matches)) {
            echo "   - {$matches[1]} : OK\n";
        }
    } catch (\PDOException $e) {
        echo "   ERREUR : " . $e->getMessage() . "\n";
        echo "   SQL : " . substr($sql, 0, 80) . "...\n";
    }
}

// 3. Create admin_users table and seed admin
echo "\n3. Configuration admin...\n";
echo "   Création de la table admin_users... ";
AdminUser::createTable();
echo "OK\n";

echo "   Ajout de l'admin ({$adminEmail})... ";
AdminUser::seedDefaultAdmin($adminEmail);
echo "OK\n";

// 4. Verification
echo "\n4. Vérification finale...\n";
$tables = $pdo->query("SHOW TABLES")->fetchAll(\PDO::FETCH_COLUMN);
echo "   Tables présentes : " . count($tables) . "\n";
foreach ($tables as $table) {
    echo "   - {$table}\n";
}

$requiredTables = ['articles', 'article_revisions', 'leads', 'admin_users', 'newsletter_subscribers'];
$missing = array_diff($requiredTables, $tables);
if (!empty($missing)) {
    echo "\n   ATTENTION : tables manquantes : " . implode(', ', $missing) . "\n";
} else {
    echo "\n   Toutes les tables requises sont présentes.\n";
}

echo "\n=== Initialisation terminée ===\n";
echo "Connectez-vous sur : " . Config::get('base_url', '') . "/admin/login\n";
