<?php

/**
 * Migration: Lead Pipeline + Partenaires + Portfolio.
 *
 * Extends leads table with pipeline statuses, commission fields,
 * and creates the partenaires table for partner management.
 *
 * Safe to run multiple times — checks for existing columns/tables.
 *
 * Usage: php database/migrate-pipeline.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../app/core/bootstrap.php';

use App\Core\Database;

echo "=== Migration: Lead Pipeline + Partenaires ===\n\n";

try {
    $pdo = Database::connection();
    echo "1. Connexion DB... OK\n\n";
} catch (\Throwable $e) {
    echo "ERREUR: Impossible de se connecter à la base de données.\n";
    echo "   " . $e->getMessage() . "\n";
    exit(1);
}

// Helper: check if a column exists in a table
function columnExists(PDO $pdo, string $table, string $column): bool
{
    $stmt = $pdo->prepare(
        'SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table AND COLUMN_NAME = :col'
    );
    $stmt->execute([':table' => $table, ':col' => $column]);
    return (int) $stmt->fetchColumn() > 0;
}

// Helper: check if a table exists
function tableExists(PDO $pdo, string $table): bool
{
    $stmt = $pdo->prepare(
        'SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table'
    );
    $stmt->execute([':table' => $table]);
    return (int) $stmt->fetchColumn() > 0;
}

// Helper: check if an index exists
function indexExists(PDO $pdo, string $table, string $indexName): bool
{
    $stmt = $pdo->prepare('SHOW INDEX FROM ' . $table . ' WHERE Key_name = :idx');
    $stmt->execute([':idx' => $indexName]);
    return $stmt->rowCount() > 0;
}

$steps = 0;

// --- Step 1: Extend leads.statut ENUM to full pipeline ---
echo "2. Extension de leads.statut (pipeline complet)...\n";
try {
    $pdo->exec("
        ALTER TABLE leads
          MODIFY COLUMN statut ENUM(
            'nouveau',
            'contacte',
            'rdv_pris',
            'visite_realisee',
            'mandat_simple',
            'mandat_exclusif',
            'compromis_vente',
            'signe',
            'co_signature_partenaire',
            'assigne_autre'
          ) NOT NULL DEFAULT 'nouveau'
    ");
    echo "   leads.statut ENUM... mis à jour.\n";
    $steps++;
} catch (\PDOException $e) {
    echo "   leads.statut... " . $e->getMessage() . "\n";
}

// --- Step 2: Add partner and commission fields to leads ---
echo "\n3. Ajout des colonnes pipeline à leads...\n";

$leadsColumns = [
    'partenaire_id'    => "ALTER TABLE leads ADD COLUMN partenaire_id INT UNSIGNED NULL AFTER notes",
    'commission_taux'  => "ALTER TABLE leads ADD COLUMN commission_taux DECIMAL(5,2) NULL DEFAULT NULL AFTER partenaire_id",
    'commission_montant' => "ALTER TABLE leads ADD COLUMN commission_montant DECIMAL(12,2) NULL DEFAULT NULL AFTER commission_taux",
    'assigne_a'        => "ALTER TABLE leads ADD COLUMN assigne_a VARCHAR(180) NULL DEFAULT NULL AFTER commission_montant",
    'date_mandat'      => "ALTER TABLE leads ADD COLUMN date_mandat DATE NULL DEFAULT NULL AFTER assigne_a",
    'date_compromis'   => "ALTER TABLE leads ADD COLUMN date_compromis DATE NULL DEFAULT NULL AFTER date_mandat",
    'date_signature'   => "ALTER TABLE leads ADD COLUMN date_signature DATE NULL DEFAULT NULL AFTER date_compromis",
    'prix_vente'       => "ALTER TABLE leads ADD COLUMN prix_vente DECIMAL(12,2) NULL DEFAULT NULL AFTER date_signature",
];

foreach ($leadsColumns as $col => $sql) {
    if (columnExists($pdo, 'leads', $col)) {
        echo "   leads.{$col}... déjà présente.\n";
        continue;
    }
    try {
        $pdo->exec($sql);
        echo "   leads.{$col}... ajoutée.\n";
        $steps++;
    } catch (\PDOException $e) {
        echo "   leads.{$col}... ERREUR: " . $e->getMessage() . "\n";
    }
}

// Add indexes
$leadsIndexes = [
    'idx_partenaire_id' => 'ALTER TABLE leads ADD INDEX idx_partenaire_id (partenaire_id)',
    'idx_date_signature' => 'ALTER TABLE leads ADD INDEX idx_date_signature (date_signature)',
];

foreach ($leadsIndexes as $idxName => $sql) {
    if (indexExists($pdo, 'leads', $idxName)) {
        echo "   Index {$idxName}... déjà présent.\n";
        continue;
    }
    try {
        $pdo->exec($sql);
        echo "   Index {$idxName}... ajouté.\n";
        $steps++;
    } catch (\PDOException $e) {
        echo "   Index {$idxName}... ERREUR: " . $e->getMessage() . "\n";
    }
}

// --- Step 3: Create partenaires table ---
echo "\n4. Création de la table partenaires...\n";

if (tableExists($pdo, 'partenaires')) {
    echo "   Table partenaires... déjà présente.\n";
} else {
    try {
        $pdo->exec("
            CREATE TABLE partenaires (
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
        ");
        echo "   Table partenaires... CRÉÉE.\n";
        $steps++;
    } catch (\PDOException $e) {
        echo "   Table partenaires... ERREUR: " . $e->getMessage() . "\n";
    }
}

// --- Step 4: Add foreign key ---
echo "\n5. Clé étrangère leads.partenaire_id -> partenaires.id...\n";

$fkExists = false;
try {
    $stmt = $pdo->prepare("
        SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = 'leads'
          AND CONSTRAINT_NAME = 'fk_leads_partenaire'
          AND CONSTRAINT_TYPE = 'FOREIGN KEY'
    ");
    $stmt->execute();
    $fkExists = (int) $stmt->fetchColumn() > 0;
} catch (\PDOException) {
    // Ignore check errors
}

if ($fkExists) {
    echo "   FK fk_leads_partenaire... déjà présente.\n";
} else {
    try {
        $pdo->exec("
            ALTER TABLE leads
              ADD CONSTRAINT fk_leads_partenaire
                FOREIGN KEY (partenaire_id) REFERENCES partenaires(id)
                ON DELETE SET NULL
        ");
        echo "   FK fk_leads_partenaire... ajoutée.\n";
        $steps++;
    } catch (\PDOException $e) {
        echo "   FK fk_leads_partenaire... ERREUR: " . $e->getMessage() . "\n";
    }
}

// --- Summary ---
echo "\n=== Migration terminée ({$steps} modification(s)) ===\n";
