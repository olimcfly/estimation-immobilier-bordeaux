<?php

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../includes/admin-auth.php';
require_once __DIR__ . '/../../includes/database.php';

header('Content-Type: application/json; charset=utf-8');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée.'], JSON_UNESCAPED_UNICODE);
    exit;
}

if ((string) ($_SESSION['admin_email'] ?? '') !== 'superuser@estimation-immobilier-bordeaux.fr') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Accès refusé.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$raw = file_get_contents('php://input');
$payload = json_decode($raw ?: '{}', true);
$token = (string) ($payload['csrf_token'] ?? '');
$moduleId = (int) ($payload['module_id'] ?? 0);

if ($token === '' || !hash_equals((string) ($_SESSION['csrf_token'] ?? ''), $token)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Token CSRF invalide.'], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($moduleId <= 0) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Module invalide.'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $db = Database::getConnection();
    $stmt = $db->prepare('UPDATE modules SET is_active = IF(is_active = 1, 0, 1) WHERE id = :id');
    $stmt->execute(['id' => $moduleId]);

    echo json_encode(['success' => true, 'message' => 'Module mis à jour.'], JSON_UNESCAPED_UNICODE);
} catch (Throwable $exception) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur serveur.', 'error' => $exception->getMessage()], JSON_UNESCAPED_UNICODE);
}
