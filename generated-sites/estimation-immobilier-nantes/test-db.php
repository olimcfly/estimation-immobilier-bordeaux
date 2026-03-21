<?php

declare(strict_types=1);

require_once __DIR__ . '/app/core/bootstrap.php';

use App\Core\Config;
use App\Core\Database;

header('Content-Type: text/plain; charset=utf-8');

echo "=== Diagnostic Base de Données ===\n\n";

// 1. Vérifier la présence du fichier .env
$envFile = __DIR__ . '/.env';
echo "1. Fichier .env : " . (is_file($envFile) ? 'PRESENT' : 'ABSENT - Copiez .env.example en .env et configurez-le') . "\n";

// 2. Afficher la config DB (sans le mot de passe)
echo "\n2. Configuration DB :\n";
echo "   Host    : " . Config::get('db.host', '(non défini)') . "\n";
echo "   Port    : " . Config::get('db.port', '(non défini)') . "\n";
echo "   Name    : " . Config::get('db.name', '(non défini)') . "\n";
echo "   User    : " . Config::get('db.user', '(non défini)') . "\n";
echo "   Pass    : " . (Config::get('db.pass', '') !== '' ? '(défini)' : '(VIDE)') . "\n";
echo "   Charset : " . Config::get('db.charset', '(non défini)') . "\n";

// 3. Tester la connexion
echo "\n3. Test de connexion : ";
try {
    $pdo = Database::connection();
    echo "OK\n";
} catch (\Throwable $e) {
    echo "ECHEC\n";
    echo "   Erreur : " . $e->getMessage() . "\n";
    echo "\n=== Vérifiez vos identifiants DB dans le fichier .env ===\n";
    exit(1);
}

// 4. Vérifier les tables
echo "\n4. Tables existantes :\n";
$tables = $pdo->query("SHOW TABLES")->fetchAll(\PDO::FETCH_COLUMN);
if (empty($tables)) {
    echo "   AUCUNE TABLE - Exécutez : mysql -u USER -p DB_NAME < database/schema.sql\n";
} else {
    foreach ($tables as $table) {
        echo "   - {$table}\n";
    }
}

// 5. Vérifier les tables requises par l'application
echo "\n5. Tables requises :\n";
$requiredTables = ['articles', 'article_revisions', 'leads', 'admin_users', 'newsletter_subscribers'];
$missingTables = [];
foreach ($requiredTables as $required) {
    $exists = in_array($required, $tables, true);
    echo "   - {$required} : " . ($exists ? 'OK' : 'ABSENTE') . "\n";
    if (!$exists) {
        $missingTables[] = $required;
    }
}

if (!empty($missingTables)) {
    echo "\n   Tables manquantes détectées.\n";
    echo "   Exécutez : php database/setup.php\n";
}

// 6. Vérifier la table admin_users en détail
echo "\n6. Table admin_users :\n";
if (!in_array('admin_users', $tables, true)) {
    echo "   ABSENTE - Exécutez : php database/setup.php\n";
} else {
    $columns = $pdo->query("SHOW COLUMNS FROM admin_users")->fetchAll(\PDO::FETCH_COLUMN);
    echo "   Colonnes : " . implode(', ', $columns) . "\n";

    if (!in_array('login_code', $columns, true)) {
        echo "   ATTENTION : colonne 'login_code' manquante ! La table doit être recréée.\n";
        echo "   Exécutez : php database/setup.php\n";
    }

    $count = $pdo->query("SELECT COUNT(*) FROM admin_users")->fetchColumn();
    echo "   Nombre d'admins : {$count}\n";

    if ((int) $count === 0) {
        echo "   AUCUN ADMIN - Exécutez : php database/setup.php\n";
    } else {
        $admins = $pdo->query("SELECT email FROM admin_users")->fetchAll(\PDO::FETCH_COLUMN);
        foreach ($admins as $email) {
            echo "   - {$email}\n";
        }
    }
}

// 7. Test de santé via Database::ping()
echo "\n7. Health check (ping) : ";
echo Database::ping() ? "OK\n" : "ECHEC\n";

echo "\n=== Diagnostic terminé ===\n";
