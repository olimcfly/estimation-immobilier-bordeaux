<?php

/**
 * Migration: Add lead_type and property details to leads table.
 * Safe to run multiple times (uses IF NOT EXISTS / column checks).
 *
 * Usage: php database/migrate-lead-types.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../app/core/bootstrap.php';

use App\Core\Database;

echo "=== Migration: Lead Types ===\n\n";

try {
    $pdo = Database::connection();
    echo "1. Connexion DB... OK\n";
} catch (\Throwable $e) {
    echo "ERREUR: Impossible de se connecter à la base de données.\n";
    echo "   " . $e->getMessage() . "\n";
    exit(1);
}

$columns = $pdo->query('SHOW COLUMNS FROM leads')->fetchAll(PDO::FETCH_COLUMN);

$migrations = [
    'lead_type' => "ALTER TABLE leads ADD COLUMN lead_type ENUM('tendance', 'qualifie') NOT NULL DEFAULT 'qualifie' AFTER website_id",
    'type_bien' => "ALTER TABLE leads ADD COLUMN type_bien VARCHAR(80) NULL AFTER ville",
    'surface_m2' => "ALTER TABLE leads ADD COLUMN surface_m2 DECIMAL(8,2) NULL AFTER type_bien",
    'pieces' => "ALTER TABLE leads ADD COLUMN pieces INT UNSIGNED NULL AFTER surface_m2",
];

foreach ($migrations as $col => $sql) {
    if (in_array($col, $columns, true)) {
        echo "2. Colonne '{$col}'... déjà présente, ignorée.\n";
        continue;
    }
    $pdo->exec($sql);
    echo "2. Colonne '{$col}'... ajoutée.\n";
}

// Make contact fields nullable for tendance leads
$nullableCols = ['nom', 'email', 'telephone', 'adresse', 'urgence', 'motivation'];
foreach ($nullableCols as $col) {
    if (!in_array($col, $columns, true)) {
        continue;
    }
    // Get current column type
    $colInfo = $pdo->query("SHOW COLUMNS FROM leads WHERE Field = '{$col}'")->fetch(PDO::FETCH_ASSOC);
    if ($colInfo && $colInfo['Null'] === 'YES') {
        echo "3. Colonne '{$col}' nullable... déjà OK.\n";
        continue;
    }
    $type = $colInfo['Type'] ?? 'VARCHAR(120)';
    $pdo->exec("ALTER TABLE leads MODIFY COLUMN {$col} {$type} NULL DEFAULT NULL");
    echo "3. Colonne '{$col}'... rendue nullable.\n";
}

// Add index on lead_type
$indexes = $pdo->query('SHOW INDEX FROM leads WHERE Key_name = "idx_lead_type"')->fetchAll();
if (empty($indexes)) {
    $pdo->exec('ALTER TABLE leads ADD INDEX idx_lead_type (lead_type)');
    echo "4. Index idx_lead_type... ajouté.\n";
} else {
    echo "4. Index idx_lead_type... déjà présent.\n";
}

echo "\n=== Migration terminée ===\n";
