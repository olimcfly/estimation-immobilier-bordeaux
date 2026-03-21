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
// Order matters: tables with foreign keys must come after their referenced tables.
$migrations = [
    ['articles', "
        CREATE TABLE IF NOT EXISTS articles (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            website_id INT UNSIGNED NOT NULL,
            title VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NOT NULL,
            content LONGTEXT NOT NULL,
            meta_title VARCHAR(255) NOT NULL,
            meta_description TEXT NOT NULL,
            persona VARCHAR(100) NOT NULL,
            awareness_level VARCHAR(50) NOT NULL,
            status ENUM('draft', 'published') NOT NULL DEFAULT 'draft',
            created_at DATETIME NOT NULL,
            UNIQUE KEY uq_articles_website_slug (website_id, slug),
            INDEX idx_website_id (website_id),
            INDEX idx_status_created_at (status, created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    "],

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

    ['leads', "
        CREATE TABLE IF NOT EXISTS leads (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            website_id INT UNSIGNED NOT NULL,
            lead_type ENUM('tendance', 'qualifie') NOT NULL DEFAULT 'qualifie',
            nom VARCHAR(120) NULL DEFAULT NULL,
            email VARCHAR(180) NULL DEFAULT NULL,
            telephone VARCHAR(40) NULL DEFAULT NULL,
            adresse VARCHAR(255) NULL DEFAULT NULL,
            ville VARCHAR(120) NOT NULL,
            type_bien VARCHAR(80) NULL,
            surface_m2 DECIMAL(8,2) NULL,
            pieces INT UNSIGNED NULL,
            estimation DECIMAL(12,2) NOT NULL,
            urgence VARCHAR(40) NULL DEFAULT NULL,
            motivation VARCHAR(80) NULL DEFAULT NULL,
            notes TEXT NULL,
            partenaire_id INT UNSIGNED NULL,
            commission_taux DECIMAL(5,2) NULL DEFAULT NULL,
            commission_montant DECIMAL(12,2) NULL DEFAULT NULL,
            assigne_a VARCHAR(180) NULL DEFAULT NULL,
            date_mandat DATE NULL DEFAULT NULL,
            date_compromis DATE NULL DEFAULT NULL,
            date_signature DATE NULL DEFAULT NULL,
            prix_vente DECIMAL(12,2) NULL DEFAULT NULL,
            score ENUM('chaud', 'tiede', 'froid') NOT NULL DEFAULT 'froid',
            statut ENUM(
              'nouveau', 'contacte', 'rdv_pris', 'visite_realisee',
              'mandat_simple', 'mandat_exclusif', 'compromis_vente',
              'signe', 'co_signature_partenaire', 'assigne_autre'
            ) NOT NULL DEFAULT 'nouveau',
            created_at DATETIME NOT NULL,
            INDEX idx_website_id (website_id),
            INDEX idx_lead_type (lead_type),
            INDEX idx_email (email),
            INDEX idx_statut (statut),
            INDEX idx_created_at (created_at),
            INDEX idx_partenaire_id (partenaire_id),
            INDEX idx_date_signature (date_signature)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    "],

    ['partenaires', "
        CREATE TABLE IF NOT EXISTS partenaires (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            website_id INT UNSIGNED NOT NULL,
            nom VARCHAR(180) NOT NULL,
            entreprise VARCHAR(255) NULL,
            email VARCHAR(180) NOT NULL,
            telephone VARCHAR(40) NULL,
            specialite VARCHAR(120) NULL,
            zone_geographique VARCHAR(255) NULL,
            commission_defaut DECIMAL(5,2) NULL DEFAULT 3.00,
            statut ENUM('actif', 'inactif', 'prospect') NOT NULL DEFAULT 'actif',
            notes TEXT NULL,
            nb_mandats INT UNSIGNED NOT NULL DEFAULT 0,
            ca_genere DECIMAL(12,2) NOT NULL DEFAULT 0.00,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_website_id (website_id),
            INDEX idx_statut (statut),
            INDEX idx_email (email)
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

    ['email_templates', "
        CREATE TABLE IF NOT EXISTS email_templates (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            slug VARCHAR(100) NOT NULL UNIQUE,
            name VARCHAR(255) NOT NULL,
            subject VARCHAR(255) NOT NULL,
            body_html LONGTEXT NOT NULL,
            signature TEXT NULL,
            category ENUM('notification', 'client', 'sequence', 'marketing') NOT NULL DEFAULT 'notification',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_category (category)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    "],

    ['email_logs', "
        CREATE TABLE IF NOT EXISTS email_logs (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            recipient VARCHAR(180) NOT NULL,
            subject VARCHAR(255) NOT NULL,
            body_html LONGTEXT NULL,
            status ENUM('sent', 'failed') NOT NULL DEFAULT 'sent',
            template_id INT UNSIGNED NULL,
            sent_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_recipient (recipient),
            INDEX idx_status (status),
            INDEX idx_sent_at (sent_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    "],

    ['email_sequences', "
        CREATE TABLE IF NOT EXISTS email_sequences (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            persona VARCHAR(50) NULL,
            trigger_event VARCHAR(50) NOT NULL DEFAULT 'lead_created',
            status ENUM('draft', 'active', 'paused') NOT NULL DEFAULT 'draft',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_status (status),
            INDEX idx_persona (persona)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    "],

    ['email_sequence_steps', "
        CREATE TABLE IF NOT EXISTS email_sequence_steps (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            sequence_id INT UNSIGNED NOT NULL,
            step_order INT UNSIGNED NOT NULL DEFAULT 1,
            delay_days INT UNSIGNED NOT NULL DEFAULT 0,
            subject VARCHAR(255) NOT NULL,
            body_html LONGTEXT NOT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_sequence_order (sequence_id, step_order),
            CONSTRAINT fk_seq_steps_sequence
                FOREIGN KEY (sequence_id) REFERENCES email_sequences(id)
                ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    "],

    ['lead_personas', "
        CREATE TABLE IF NOT EXISTS lead_personas (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            lead_id INT UNSIGNED NOT NULL UNIQUE,
            neuropersona VARCHAR(50) NULL,
            bant_budget TEXT NULL,
            bant_authority TEXT NULL,
            bant_need TEXT NULL,
            bant_timeline TEXT NULL,
            notes TEXT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_neuropersona (neuropersona),
            CONSTRAINT fk_persona_lead
                FOREIGN KEY (lead_id) REFERENCES leads(id)
                ON DELETE CASCADE
    ['actualites', "
        CREATE TABLE IF NOT EXISTS actualites (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            website_id INT UNSIGNED NOT NULL,
            title VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NOT NULL,
            content LONGTEXT NOT NULL,
            excerpt TEXT NOT NULL DEFAULT '',
            meta_title VARCHAR(255) NOT NULL DEFAULT '',
            meta_description TEXT NOT NULL DEFAULT '',
            image_url VARCHAR(500) DEFAULT NULL,
            image_prompt TEXT DEFAULT NULL,
            source_query TEXT DEFAULT NULL,
            source_results LONGTEXT DEFAULT NULL,
            status ENUM('draft', 'published') NOT NULL DEFAULT 'draft',
            generated_by ENUM('manual', 'ai', 'cron') NOT NULL DEFAULT 'manual',
            published_at DATETIME DEFAULT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY uq_actualites_website_slug (website_id, slug),
            INDEX idx_actualites_website (website_id),
            INDEX idx_actualites_status (status, published_at),
            INDEX idx_actualites_created (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    "],

    ['actualites_cron_log', "
        CREATE TABLE IF NOT EXISTS actualites_cron_log (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            website_id INT UNSIGNED NOT NULL,
            query_used TEXT NOT NULL,
            articles_found INT UNSIGNED NOT NULL DEFAULT 0,
            article_published_id INT UNSIGNED DEFAULT NULL,
            status ENUM('success', 'error') NOT NULL DEFAULT 'success',
            error_message TEXT DEFAULT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_cron_log_website (website_id, created_at)
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
    $adminEmail = $_ENV['ADMIN_EMAIL'] ?? 'contact@estimation-immobilier-nandy.fr';
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
$requiredTables = ['articles', 'article_revisions', 'leads', 'partenaires', 'admin_users', 'newsletter_subscribers', 'design_templates', 'email_templates', 'email_logs', 'email_sequences', 'email_sequence_steps', 'lead_personas'];
$requiredTables = ['articles', 'article_revisions', 'leads', 'admin_users', 'newsletter_subscribers', 'design_templates', 'actualites', 'actualites_cron_log'];
$missing = array_diff($requiredTables, $finalTables);

if (empty($missing)) {
    echo "\n  Toutes les tables requises sont présentes.\n";
} else {
    echo "\n  ATTENTION — tables encore manquantes : " . implode(', ', $missing) . "\n";
}

echo "\n=== Migration terminée ===\n";
