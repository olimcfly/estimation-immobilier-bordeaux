<?php

/**
 * Migration script to add missing tables to an existing database.
 *
 * Safe to run multiple times — uses CREATE TABLE IF NOT EXISTS.
 *
 * Usage: php database/migrate.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../app/core/bootstrap.php';

use App\Core\Database;

echo "=== Migration : ajout des tables manquantes ===\n\n";

// 1. Test connection
echo "Connexion... ";
try {
    $pdo = Database::connection();
    echo "OK\n\n";
} catch (\Throwable $e) {
    echo "ECHEC : " . $e->getMessage() . "\n";
    exit(1);
}

// Get existing tables
$existingTables = $pdo->query("SHOW TABLES")->fetchAll(\PDO::FETCH_COLUMN);
echo "Tables existantes : " . implode(', ', $existingTables) . "\n\n";

// Define migrations — each entry is [table_name, SQL]
$migrations = [
    ['article_revisions', "
        CREATE TABLE IF NOT EXISTS article_revisions (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            article_id INT UNSIGNED NOT NULL,
            revision_number INT UNSIGNED NOT NULL,
            title VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NOT NULL,
            content LONGTEXT NOT NULL,
            meta_title VARCHAR(255) NOT NULL,
            meta_description TEXT NOT NULL,
            persona VARCHAR(100) NOT NULL,
            awareness_level VARCHAR(50) NOT NULL,
            status ENUM('draft', 'published') NOT NULL DEFAULT 'draft',
            created_at DATETIME NOT NULL,
            UNIQUE KEY uniq_article_revision (article_id, revision_number),
            INDEX idx_article_created_at (article_id, created_at),
            CONSTRAINT fk_article_revisions_article
                FOREIGN KEY (article_id) REFERENCES articles(id)
                ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    "],

    ['admin_users', "
        CREATE TABLE IF NOT EXISTS admin_users (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(180) NOT NULL UNIQUE,
            name VARCHAR(120) NOT NULL DEFAULT '',
            login_code VARCHAR(255) DEFAULT NULL,
            login_code_expires_at DATETIME DEFAULT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_admin_email (email)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    "],

    ['newsletter_subscribers', "
        CREATE TABLE IF NOT EXISTS newsletter_subscribers (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(180) NOT NULL UNIQUE,
            confirmed_at DATETIME NOT NULL,
            created_at DATETIME NOT NULL,
            INDEX idx_newsletter_confirmed_at (confirmed_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    "],

    ['design_templates', "
        CREATE TABLE IF NOT EXISTS design_templates (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            slug VARCHAR(100) NOT NULL UNIQUE,
            name VARCHAR(255) NOT NULL DEFAULT '',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    "],
];

// Run migrations
$created = 0;
$skipped = 0;

foreach ($migrations as [$table, $sql]) {
    $exists = in_array($table, $existingTables, true);
    echo "  {$table} : ";

    if ($exists) {
        echo "existe déjà, ignorée\n";
        $skipped++;
        continue;
    }

    try {
        $pdo->exec($sql);
        echo "CRÉÉE\n";
        $created++;
    } catch (\PDOException $e) {
        echo "ERREUR - " . $e->getMessage() . "\n";
    }
}

// Seed default admin if table was just created
if (!in_array('admin_users', $existingTables, true)) {
    $adminEmail = $_ENV['ADMIN_EMAIL'] ?? 'contact@estimation-immobilier-bordeaux.fr';
    echo "\n  Ajout de l'admin par défaut ({$adminEmail})... ";
    $stmt = $pdo->prepare(
        'INSERT IGNORE INTO admin_users (email, name, created_at) VALUES (:email, :name, NOW())'
    );
    $stmt->execute(['email' => $adminEmail, 'name' => 'Administrateur']);
    echo "OK\n";
}

// Summary
echo "\n=== Résultat ===\n";
echo "  Tables créées  : {$created}\n";
echo "  Tables ignorées : {$skipped}\n";

// Final verification
$finalTables = $pdo->query("SHOW TABLES")->fetchAll(\PDO::FETCH_COLUMN);
$requiredTables = ['articles', 'article_revisions', 'leads', 'admin_users', 'newsletter_subscribers', 'design_templates'];
$missing = array_diff($requiredTables, $finalTables);

if (empty($missing)) {
    echo "\n  Toutes les tables requises sont présentes.\n";
} else {
    echo "\n  ATTENTION — tables encore manquantes : " . implode(', ', $missing) . "\n";
}

echo "\n=== Migration terminée ===\n";
