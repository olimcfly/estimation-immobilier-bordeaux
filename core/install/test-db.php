<?php
declare(strict_types=1);

session_start();
header('Content-Type: application/json; charset=utf-8');

$rootDir = dirname(__DIR__);
$installSqlPath = $rootDir . '/install.sql';
$createLeadsSqlPath = $rootDir . '/sql/create_leads_table.sql';

function tableExists(PDO $pdo, string $tableName): bool
{
    $stmt = $pdo->prepare(
        'SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = :table_name'
    );
    $stmt->execute(['table_name' => $tableName]);

    return (int) $stmt->fetchColumn() > 0;
}

function applySqlFileIfTableMissing(PDO $pdo, string $tableName, string $sqlPath): void
{
    if (tableExists($pdo, $tableName)) {
        return;
    }
    if (!is_file($sqlPath)) {
        throw new RuntimeException(basename($sqlPath) . ' introuvable.');
    }
    $sql = trim((string) file_get_contents($sqlPath));
    if ($sql === '') {
        throw new RuntimeException(basename($sqlPath) . ' est vide.');
    }
    $pdo->exec($sql);
}

$host = trim((string) ($_POST['host'] ?? 'localhost'));
$dbName = trim((string) ($_POST['db_name'] ?? ''));
$dbUser = trim((string) ($_POST['db_user'] ?? ''));
$dbPass = (string) ($_POST['db_pass'] ?? '');

if ($dbName === '' || $dbUser === '') {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Nom de base et utilisateur requis.']);
    exit;
}

try {
    $pdo = new PDO(
        sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', $host, $dbName),
        $dbUser,
        $dbPass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    if (!is_file($installSqlPath)) {
        throw new RuntimeException('Fichier install.sql introuvable.');
    }

    $sql = (string) file_get_contents($installSqlPath);
    if ($sql === '') {
        throw new RuntimeException('Le fichier install.sql est vide.');
    }

    $pdo->exec($sql);
    applySqlFileIfTableMissing($pdo, 'leads', $createLeadsSqlPath);

    $_SESSION['install_db'] = [
        'host' => $host,
        'db_name' => $dbName,
        'db_user' => $dbUser,
        'db_pass' => $dbPass,
    ];

    echo json_encode(['success' => true, 'message' => 'Connexion OK et schéma SQL appliqué.']);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
