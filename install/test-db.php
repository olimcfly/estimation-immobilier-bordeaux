<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

function s(?string $value): string
{
    return htmlspecialchars(trim((string) $value), ENT_QUOTES, 'UTF-8');
}

$host = s($_POST['db_host'] ?? 'localhost');
$dbName = s($_POST['db_name'] ?? '');
$dbUser = s($_POST['db_user'] ?? '');
$dbPass = s($_POST['db_pass'] ?? '');

if ($dbName === '' || $dbUser === '') {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'DB_NAME et DB_USER sont requis.']);
    exit;
}

try {
    $pdo = new PDO(
        sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', $host, $dbName),
        $dbUser,
        $dbPass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    $pdo->query('SELECT 1');

    echo json_encode(['success' => true, 'message' => 'Connexion DB réussie.']);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur DB : ' . $e->getMessage()]);
}
