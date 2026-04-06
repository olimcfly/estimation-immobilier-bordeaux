<?php

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../includes/admin-auth.php';
require_once __DIR__ . '/../../includes/security.php';
require_once __DIR__ . '/../../includes/database.php';

header('Content-Type: application/json; charset=utf-8');

try {
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Méthode non autorisée.'], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
        exit;
    }

    if (!verifyCsrf()) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Token CSRF invalide.'], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
        exit;
    }

    $adminRole = (string) ($_SESSION['admin_role'] ?? '');
    if ($adminRole !== 'superadmin') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Accès refusé.'], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
        exit;
    }

    $userId = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
    $isActiveInput = filter_input(INPUT_POST, 'is_active', FILTER_DEFAULT, FILTER_REQUIRE_SCALAR);

    if ($userId === false || $userId === null || $userId <= 0 || $isActiveInput === null || $isActiveInput === false) {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'Données invalides.'], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
        exit;
    }

    $isActive = filter_var($isActiveInput, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    if ($isActive === null) {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'Valeur d\'activation invalide.'], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
        exit;
    }

    $db = Database::getConnection();
    $stmt = $db->prepare('UPDATE `admins` SET `is_active` = :is_active WHERE `id` = :id');
    $stmt->bindValue(':is_active', $isActive ? 1 : 0, PDO::PARAM_INT);
    $stmt->bindValue(':id', $userId, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Utilisateur introuvable ou inchangé.'], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
        exit;
    }

    echo json_encode([
        'success' => true,
        'user_id' => $userId,
        'is_active' => $isActive,
    ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Une erreur est survenue lors de la mise à jour.',
        'error' => $e->getMessage(),
    ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
}
