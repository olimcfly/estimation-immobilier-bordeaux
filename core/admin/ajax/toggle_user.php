<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/admin-auth.php';
require_once __DIR__ . '/../../includes/database.php';

header('Content-Type: application/json; charset=utf-8');

try {
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Méthode non autorisée.'], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
        exit;
    }

    if ((string) ($_SESSION['admin_role'] ?? '') !== 'superadmin') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Accès superadmin requis.'], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
        exit;
    }

    $tokenHeader = (string) ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    $tokenSession = (string) ($_SESSION['csrf_token'] ?? '');
    if ($tokenSession === '' || !hash_equals($tokenSession, $tokenHeader)) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Token CSRF invalide.'], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
        exit;
    }

    $userId = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
    $isActiveRaw = (string) ($_POST['is_active'] ?? '');

    if ($userId === false || $userId === null || $userId <= 0) {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'Utilisateur invalide.'], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
        exit;
    }

    if ($isActiveRaw !== '0' && $isActiveRaw !== '1') {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'Valeur is_active invalide.'], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
        exit;
    }

    $db = Database::getConnection();

    $columnStmt = $db->prepare(
        'SELECT COLUMN_NAME
         FROM information_schema.COLUMNS
         WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table_name AND COLUMN_NAME = :column_name
         LIMIT 1'
    );
    $columnStmt->execute(['table_name' => 'admins', 'column_name' => 'is_active']);
    $hasIsActiveColumn = (bool) $columnStmt->fetchColumn();

    if (!$hasIsActiveColumn) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'La colonne admins.is_active est absente. Ajoutez-la avant d’utiliser cette action.',
        ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
        exit;
    }

    $update = $db->prepare('UPDATE admins SET is_active = :is_active WHERE id = :id LIMIT 1');
    $update->execute([
        'is_active' => (int) $isActiveRaw,
        'id' => $userId,
    ]);

    if ($update->rowCount() < 1) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Utilisateur introuvable ou déjà à jour.'], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
        exit;
    }

    echo json_encode([
        'success' => true,
        'message' => 'Utilisateur mis à jour avec succès.',
    ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
} catch (Throwable $exception) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur serveur pendant la mise à jour de l’utilisateur.',
    ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
}
